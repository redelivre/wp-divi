import { isBuilderUsed } from 'gutenberg/utils/helpers';
import { select, dispatch } from '@wordpress/data';

const { isCleanNewPost } = select('core/editor');
const { updateEditorSettings } = dispatch('core/editor');

const toggleClass = (limited = true) => {
  const { classList } = document.getElementById('editor');
  classList[limited ? 'add' : 'remove']('et-limited-ui');
  classList[isCleanNewPost() ? 'add' : 'remove']('et-new-post');
  classList[isBuilderUsed() ? 'add' : 'remove']('et-builder-used');
};

const toggleLock = (lock = true) => {
  toggleClass(lock);
  updateEditorSettings({ templateLock: lock });
};

const lock = () => toggleLock(true);
const unlock = () => toggleLock(false);

export {
  lock,
  unlock,
};
