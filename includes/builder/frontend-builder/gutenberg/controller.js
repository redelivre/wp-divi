import React from 'react';
import placeholder from 'gutenberg/blocks/placeholder';
import { isBuilderUsed, isScriptDebug, canToggle, isEnabled, getVBUrl } from 'gutenberg/utils/helpers';
import { DIVI, GUTENBERG } from 'gutenberg/constants';
import { registerBlock, unregisterBlock } from 'gutenberg/blocks/registration';
import get from 'lodash/get';
import throttle from 'lodash/throttle';
import isEmpty from 'lodash/isEmpty';
import startsWith from 'lodash/startsWith';
import { __ } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';
import { dispatch, select, subscribe } from '@wordpress/data';
import { cold } from 'react-hot-loader';
import { RichText } from '@wordpress/editor';
import { renderToString, RawHTML } from '@wordpress/element';

// react-hot-loader replaces the Component with ProxyComponent altering its type.
// Since Gutenberg compares types while serializing content, we need to disable
// hot reloading for RichText and RawHTML
cold(RichText.Content);
cold(RawHTML);

const { setupEditor, editPost } = dispatch('core/editor');
const { isCleanNewPost, getCurrentPost, getCurrentPostType, getEditedPostAttribute, getEditedPostContent, getBlocks } = select('core/editor');
const { getEditorMode } = select('core/edit-post');
const { switchEditorMode } = dispatch('core/edit-post');

const registerPlaceholder = () => {
  registerBlock(placeholder);
};
const unregisterPlaceholder = () => {
  unregisterBlock(placeholder);
};
const hasPlaceholder = () => {
  const blocks = getBlocks();
  if (blocks.length !== 1) {
    return false;
  }
  return get(blocks, '0.name') === placeholder.name;
};
// Throttle this just to avoid potential loops, better safe than sorry.
const switchToVisualMode = throttle(() => switchEditorMode('visual'), 100);

const button = renderToString((
  <div className="et-buttons">
    <button type="button" className="is-button is-default is-large components-button editor-post-switch-to-divi" data-editor={DIVI}>
      {__('Use The Divi Builder')}
    </button>
    <button type="button" className="is-button is-default is-large components-button editor-post-switch-to-gutenberg" data-editor={GUTENBERG}>
      {__('Return To Default Editor')}
    </button>
  </div>
));

const $page_template_meta_boxes = $('.page-template-options').closest('.postbox');

const updateExtraPageTemplateMetaboxes = () => {
  const pageTemplate = getEditedPostAttribute( 'template' );

  const current_page_template = pageTemplate;

  $page_template_meta_boxes.each(function () {
    const this_page_template = $(this).find(".page-template-options").val();

    if (this_page_template === current_page_template) {
      if (!$(this).is(':visible')) {
        $(this).show();
      }
    } else {
      $(this).hide();
    }
  });

  if ($page_template_meta_boxes.is(':visible')) {
    $('#et_pb_layout').removeClass('first-visible');
  } else {
    $('#et_pb_layout').addClass('first-visible');
  }
};

// Toggle visible Page Settings in the `Divi Page Settings` metabox depending on Builder state
const toggleMetaSettings = (isBuilderUsed) => {
  const $metaboxID = jQuery('#et_settings_meta_box_gutenberg');
  const $pageSettings = jQuery('.et_pb_page_setting');
  const options = get(window, 'et_builder_gutenberg.helpers');
  const postType = getCurrentPostType();
  const postFormat = getEditedPostAttribute('format');

  // Toggle classname on body. Some styling needs to be CSS-based because the DOM is generated on the fly
  // as react component and there's no feasible way to modify it besides CSS overwrite (ie: post format)
  jQuery('body').toggleClass('et-builder-on-gutenberg', isBuilderUsed);

  // Dot navigation. Visible when builder is used
  $metaboxID.find('.et_pb_side_nav_settings').toggle(isBuilderUsed);

  // Post Title. When exist (post post-type only), visible when builder is used
  $metaboxID.find('.et_pb_single_title').toggle(isBuilderUsed);

  // Page Layout. Visible on Gutenberg. Also visible on builder on selected post type (post and product/custom post type)
  const isPageLayoutVisible = $pageSettings.filter(':visible').length > 1 || !(isBuilderUsed && 'post' !== postType && 'no' === get(options, 'is3rdPartyPostType'));
  $metaboxID.find('.et_pb_page_layout_settings').toggle(isPageLayoutVisible);

  // Post format related options (Use background color, select color, and text color) is only visible on gutenberg
  // on post post-type when audio, quote, or link format is selected
  if (postFormat) {
    jQuery('.et_divi_format_setting').hide();
    jQuery(`.et_divi_format_setting.et_divi_${postFormat}_settings`).toggle(!isBuilderUsed);
  }

  // Project Navigation. Visible on builder on project post type
  $metaboxID.find('.et_pb_project_nav').toggle(isBuilderUsed && 'project' === postType);
};

class Controller {
  init = () => {
    registerPlaceholder();
    this.gbContent = '';
    this.gbReady = false;
    this.prevPostFormat = '';
    this.unsubscribe = subscribe(this.onEditorContentChange);
    subscribe(this.onEditorModeChange);
    subscribe(this.onPageTemplateChange);
  }

  onClick = (e) => {
    switch (e.target.getAttribute('data-editor')) {
      case DIVI: {
        this.addPlaceholder(getEditedPostContent());

        toggleMetaSettings(true);
        break;
      }
      default: {
        // Okay, this button was inside the placeholder but then was moved out of it.
        // That logic is a massive PITA, no time to refactor so this will have to do for now.
        // NOTE: this event has no toggleMetaSettings() because it is added on switchEditor() to cover both header
        // and placeholder button scenario
        jQuery('#et-switch-to-gutenberg').click();
      }
    }
  }

  addPlaceholder = (content = '') => {
    registerPlaceholder();
    this.gbContent = content;
    this.setupEditor(applyFilters('divi.addPlaceholder', content));
  }

  getGBContent = () => this.gbContent;

  setupEditor = (raw, title = false) => {
    const post = getCurrentPost();
    // Set post content
    setupEditor({ ...post, content: { raw } });
    if (title !== false && title !== post.title) {
      // Set post title
      editPost({ title });
    }
  }

  switchEditor = (editor, content) => {
    switch (editor) {
      case DIVI: {
        // Open VB
        window.location.href = getVBUrl();
        break;
      }
      default: {
        const title = getEditedPostAttribute('title');
        // Restore GB content and title (without saving)
        this.setupEditor(placeholder.unwrap(content), title);
        unregisterPlaceholder();

        toggleMetaSettings(false);
      }
    }
  }

  addButton = () => {
    // Add the custom button
    setTimeout(() => jQuery(button).on('click', 'button', this.onClick).insertAfter('.edit-post-header-toolbar'), 0);
  }

  fireEditorReadyEvent = () => {
    // fire event once. Exit if it was fired already
    if (this.gbReady) {
      return;
    }

    let event;

    if ('function' !== typeof(Event)) {
      event = document.createEvent('Event');

      event.initEvent('ETGBReady', true, true);
    } else {
      event = new Event('ETGBReady');
    }

    // Once editor is ready, before gutenberg / builder is picked on placeholder, builder is "selected" by default
    toggleMetaSettings(true);

    // Post format only exist on post post-type
    // subscribe() should be called once post format data is ready
    if ('post' === getCurrentPostType()) {
      subscribe(this.onPostFormatChange);
    }

    document.dispatchEvent(event);
  }

  onEditorContentChange = () => {
    const post = getCurrentPost();
    if (isEmpty(post) || !this.unsubscribe) {
      // If we don't have a post, GB isn't ready yet
      return;
    }
    if (canToggle()) {
      this.addButton();
    }

    this.fireEditorReadyEvent();

    // We only need to do this step once
    this.unsubscribe();
    this.unsubscribe = false;
    const content = get(post, 'content');
    if (startsWith(content, `<!-- ${placeholder.tag} `)) {
      if (!isEnabled()) {
        // If we're here, it means the post has been previously edited with Divi
        // but then support for the post type has been removed so we have to remove
        // the placeholder to avoid GB errors.
        this.setupEditor(placeholder.unwrap(content), getEditedPostAttribute('title'));
      }
      // Post content already includes placeholder tag, nothing else to do
      return;
    }
    if (isBuilderUsed() || (isCleanNewPost() && canToggle())) {
      // Add placeholder if post was edited with Divi
      if (isScriptDebug()) {
        // when SCRIPT_DEBUG is enabled, GB ends up calling `setupEditor` twice for whatever reason
        // and this causes our placeholder to be replaced with default GB content.
        // Until they fix their code, we need to ensure our own `setupEditor` is called last.
        setTimeout(() => this.addPlaceholder(content), 0);
      } else {
        this.addPlaceholder(content);
      }
    } else {
      // Unregister the placeholder block so it cannot be added via GB add block
      unregisterPlaceholder();

      // If toggle meta settings (again) to false if gutenberg is used on load
      toggleMetaSettings(false);
    }
  }

  onEditorModeChange = () => {
    const mode = getEditorMode();
    if (mode === 'text' && hasPlaceholder()) {
      switchToVisualMode();
    }
  }

  onPageTemplateChange = () => {
    const pageTemplate = getEditedPostAttribute('template');
    if (! isEmpty(pageTemplate)) {
      updateExtraPageTemplateMetaboxes();
    } else {
      $page_template_meta_boxes.each(function () {
        $(this).hide();
      });
    }
  }

  onPostFormatChange = () => {
    const postFormat = getEditedPostAttribute('format');

    // If post format value remains, bail callback
    if (this.prevPostFormat === postFormat) {
      return;
    }

    // Skip if this is initial prevPostFormat
    if ('' !== this.prevPostFormat) {
      toggleMetaSettings(false);
    }

    // Update prevPostFormat for next change check
    this.prevPostFormat = postFormat;
  }
}

const controller = new Controller();
const getGBContent = controller.getGBContent;
const switchEditor = controller.switchEditor;
controller.init();

export {
  getGBContent,
  switchEditor,
};
