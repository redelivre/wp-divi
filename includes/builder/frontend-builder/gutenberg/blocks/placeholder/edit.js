import { hot } from 'react-hot-loader';
import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { DIVI, GUTENBERG } from 'gutenberg/constants';
import { lock, unlock } from 'gutenberg/editor';
import { switchEditor, getGBContent } from 'gutenberg/controller';
import { subscribe, withDispatch, withSelect } from '@wordpress/data';
import { i18n as translations } from 'gutenberg/utils/helpers';
import { compose } from '@wordpress/compose';
import get from 'lodash/get';
import pick from 'lodash/pick';
import './editor.scss';

const { placeholder: { render: i18n } } = translations();

class edit extends Component {
  static propTypes = {
    isCleanNewPost: PropTypes.func.isRequired,
    getCurrentPost: PropTypes.func.isRequired,
    savePost: PropTypes.func.isRequired,
    setAttributes: PropTypes.func.isRequired,
    isSavingPost: PropTypes.func.isRequired,
    isSavingMetaBoxes: PropTypes.func.isRequired,
    attributes: PropTypes.object.isRequired,
  }

  constructor(props) {
    super(props);
    this.state = {
      editor: DIVI,
      isNew: props.isCleanNewPost(),
    };
  }

  componentDidMount() {
    lock();
    // Skip save when component is mounted on a newly created post
    // The draft will be saved when `Use Divi Builder` is clicked anyway
    if (!this.state.isNew) {
      this.save(this.state.editor);
    }
  }

  componentWillUnmount() {
    unlock();
  }

  getSavedMeta = () => {
    const {
      meta: {
        _et_pb_use_builder: builder,
        _et_pb_old_content: old,
      },
    } = this.props.getCurrentPost();
    return {
      builder,
      old,
    };
  }

  /**
   * Content in the GB editor when User switched to DIVI, it can be empty
   * in some case when placeholder is added automatically so always
   * return the saved old content as fallback
   *
   * @return {string}
   */
  getGBContent = () => getGBContent() || this.props.attributes.old

  save = (editor) => {
    const { attributes, setAttributes, savePost } = this.props;
    switch (editor) {
      case DIVI: {
        if (attributes.builder === 'on') {
          // If our builder was already active, do nothing
          return false;
        }

        const updates = {
          // Enable Divi.
          builder: 'on',
        };

        // Get saved meta
        const saved = this.getSavedMeta();
        // New GB content
        const content = this.getGBContent();

        if (content !== saved.old) {
          // Update content
          updates.old = content;
        }

        // Update attributes
        setAttributes(updates);

        if (saved.builder === 'on' && !updates.old) {
          // No need to auto save if User didn't save or change GB content
          // before switching back to DIVI
          return false;
        }
        // Save the post
        savePost();
        return true;
      }
      default: {
        // Disable Divi.
        setAttributes({
          // Disable Divi.
          builder: 'off',
        });
        return false;
      }
    }
  }

  divi = () => this.editWith(DIVI)
  gutenberg = () => this.editWith(GUTENBERG)

  editWith = (editor) => {
    this.setState({ editor });

    if (this.save(editor)) {
      // If post is saving, don't switch yet.
      setTimeout(() => {
        this.unsubscribe = subscribe(this.waitForSave);
      }, 0);
    } else {
      this.switchEditor(editor);
    }
  }

  // Call the controller switchEditor method and pass it the old content.
  switchEditor = editor => switchEditor(editor, this.props.attributes.old);

  isSaving = () => this.props.isSavingPost() || this.props.isSavingMetaBoxes()

  waitForSave = () => {
    if (this.isSaving() || !this.unsubscribe) {
      // If still saving, do nothing
      return;
    }
    this.unsubscribe();
    this.unsubscribe = false;
    this.switchEditor(this.state.editor);
  }

  render() {
    const { isNew } = this.state;
    const which = isNew ? 'new' : 'old';
    return (
      <div className="wp-block-divi-placeholder">
        <span className="et-icon" />
        <h3>{get(i18n.title, which)}</h3>
        <div className="et-controls">
          <button
            type="button"
            id="et-switch-to-divi"
            className="components-button is-button is-default is-large"
            onClick={this.divi}
          >
            {get(i18n.divi, which)}
          </button>
          <button
            type="button"
            id="et-switch-to-gutenberg"
            className="components-button is-button is-default is-large"
            onClick={this.gutenberg}
          >
            {i18n.default}
          </button>
        </div>
      </div>
    );
  }
}

// Create an hot reloadable HOC
export default hot(module)(compose(
  // Add 'core/editor' actions to component props
  withDispatch(dispatch => pick(dispatch('core/editor'), [
    'savePost',
  ])),
  // Add 'core/editor' selectors to HOC props
  withSelect(select => pick(select('core/editor'), [
    'isSavingPost',
    'isCleanNewPost',
    'getCurrentPost',
  ])),
  // Add 'core/edit-post' selectors to HOC props
  withSelect(select => pick(select('core/edit-post'), [
    'isSavingMetaBoxes',
  ])),
)(edit));
