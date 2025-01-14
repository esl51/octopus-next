import globals from 'globals'
import js from '@eslint/js'
import ts from 'typescript-eslint'
import vue from 'eslint-plugin-vue'
import prettier from 'eslint-plugin-prettier/recommended'

const globalsBrowser = {
  ...globals.browser,
  AudioWorkletGlobalScope: globals.browser['AudioWorkletGlobalScope '],
}
delete globalsBrowser['AudioWorkletGlobalScope ']

export default [
  {
    languageOptions: {
      ecmaVersion: 'latest',
      globals: {
        ...globalsBrowser,
        ...globals.node,
      },
    },
  },

  // js
  js.configs.recommended,
  {
    rules: {
      'no-unused-vars': [
        'error',
        {
          args: 'none',
          caughtErrors: 'none',
        },
      ],
    },
  },

  // ts
  ...ts.configs.recommended,
  {
    rules: {
      '@typescript-eslint/no-unused-vars': [
        'error',
        {
          args: 'none',
          caughtErrors: 'none',
        },
      ],
    },
  },

  // vue
  ...vue.configs['flat/recommended'],
  {
    files: ['*.vue', '**/*.vue'],
    languageOptions: {
      parserOptions: {
        parser: ts.parser,
      },
    },
  },

  // prettier
  prettier,
  {
    rules: {
      'prettier/prettier': 'warn',
    },
  },
]
