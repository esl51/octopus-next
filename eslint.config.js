import globals from 'globals'
import js from '@eslint/js'
import ts from 'typescript-eslint'
import vue from 'eslint-plugin-vue'
import sonarjs from 'eslint-plugin-sonarjs'
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

  // sonarjs
  sonarjs.configs.recommended,
  {
    rules: {
      'sonarjs/cognitive-complexity': ['error', 50],
      'sonarjs/no-hardcoded-credentials': 'off',
      'sonarjs/constructor-for-side-effects': 'off',
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
  {
    rules: {
      'vue/no-v-html': 'off',
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
