import React from 'react';
import { i18n } from 'gutenberg/utils/helpers';
import isEmpty from 'lodash/isEmpty';
import { getRawContent } from 'gutenberg/utils/data';
// We should use this... but no time for now
// import { __ } from '@wordpress/i18n';
import { RawHTML } from '@wordpress/element';
import edit from './edit';

const name = 'divi/placeholder';
const tag = `wp:${name}`;
const unwrap = content => content.replace(RegExp(`<!-- /?${tag} /?-->`, 'g'), '');
const encode = content => unwrap(content).replace(/<!-- (\/)?wp:(.+?) (\/?)-->/g, '<!-- $1divi:$2 $3-->');
const wrap = content => `<!-- ${tag} -->${encode(content)}<!-- /${tag} -->`;
const shortcode = (content) => {
  if (isEmpty(content) || content.indexOf('[et_pb_section') >= 0) {
    // If content is empty or already has shortcode, do nothing
    return content;
  }
  let modified = content;
  // Add Text Module.
  modified = `[et_pb_text]${modified}[/et_pb_text]`;
  // Add Column.
  modified = `[et_pb_column type="4_4"]${modified}[/et_pb_column]`;
  // Add Row.
  modified = `[et_pb_row]${modified}[/et_pb_row]`;
  // Add Section.
  modified = `[et_pb_section]${modified}[/et_pb_section]`;
  return modified;
};

let processed = false;
const { placeholder: { block: { title, description } } } = i18n();

const icon = (
  <svg
    aria-hidden="true"
    role="img"
    focusable="false"
    className="dashicon dashicons-format-image"
    xmlns="http://www.w3.org/2000/svg"
    width="24"
    height="24"
    viewBox="0 0 16 16"
  >
    <path
      d="M7.5,6H7v4h0.5c2.125,0,2.125-1.453,2.125-2C9.625,7.506,9.625,6,7.5,6z M8,3C5.239,3,3,5.239,3,8
         c0,2.761,2.239,5,5,5s5-2.239,5-5C13,5.239,10.761,3,8,3z M7.5,11h-1C6.224,11,6,10.761,6,10.467V5.533C6,5.239,6.224,5,6.5,5
         c0,0,0.758,0,1,0c1.241,0,3.125,0.51,3.125,3C10.625,10.521,8.741,11,7.5,11z"
    />
  </svg>

);

export default {
  name,
  tag,
  unwrap,
  settings: {
    title,
    description,
    icon,
    category: 'embed',
    useOnce: true,
    attributes: {
      content: {
        type: 'string',
        source: 'html',
      },
      builder: {
        type: 'string',
        source: 'meta',
        meta: '_et_pb_use_builder',
      },
      old: {
        type: 'string',
        source: 'meta',
        meta: '_et_pb_old_content',
      },
    },
    supports: {
      // This is needed or else GB will try to wrap the shortcode with an extra div.className
      // causing a validation error
      className: false,
      customClassName: false,
      html: false,
    },
    save: props => <RawHTML>{ props.attributes.content }</RawHTML>,
    edit,
  },
  hooks: {
    // Aight, all the following is needed because GB performs block validation
    // which will fail if the shortcode includes invalid HTML.
    // In this case, GB would show an error message instead of our custom block
    // and we can't allow that to happen.
    'divi.addPlaceholder': (content) => {
      processed = shortcode(content);
      return wrap(processed);
    },
    'blocks.getSaveElement': (element, blockType) => {
      if (blockType.name !== name) {
        return element;
      }
      return <RawHTML>{ encode(processed === false ? getRawContent() : processed) }</RawHTML>;
    },
  },
};
