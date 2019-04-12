import forEach from 'lodash/forEach';
import isArray from 'lodash/isArray';
import isUndefined from 'lodash/isUndefined';
import { getBlockType, registerBlockType, unregisterBlockType } from '@wordpress/blocks';
import { addFilter, removeFilter } from '@wordpress/hooks';

const registerBlock = (block) => {
  const blocks = isArray(block) ? block : [block];
  forEach(blocks, ({ name, settings, hooks = [] }) => {
    // Only register the block if it hasn't been done already
    if (isUndefined(getBlockType(name))) {
      registerBlockType(name, settings);
      forEach(hooks, (hook, action) => addFilter(action, name, hook));
    }
  });
};

const unregisterBlock = (block) => {
  const blocks = isArray(block) ? block : [block];
  forEach(blocks, ({ name, hooks = [] }) => {
    forEach(hooks, (hook, action) => removeFilter(action, name));
    unregisterBlockType(name);
  });
};

export {
  registerBlock,
  unregisterBlock,
};
