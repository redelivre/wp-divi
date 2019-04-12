import get from 'lodash/get';
import { getPostType, getPostID } from 'gutenberg/utils/helpers';
import { select } from '@wordpress/data';

const { getEntityRecord } = select('core');
const getPost = () => getEntityRecord('postType', getPostType(), getPostID());
const getRawContent = () => get(getPost(), 'content.raw');

export {
  getPost,
  getRawContent,
};
