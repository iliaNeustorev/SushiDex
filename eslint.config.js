import typescriptParser from '@typescript-eslint/parser';
import vue from 'eslint-plugin-vue';

export default [
    {
        ignores: [
            'resources/generated/**',
        ],
    },
    {
        files: ['resources/js/**/*.ts'],
        languageOptions: {
            parser: typescriptParser,
            parserOptions: {
                sourceType: 'module',
            },
        },
    },
    ...vue.configs['flat/base'],
    {
        files: ['resources/js/**/*.vue'],
        languageOptions: {
            parserOptions: {
                parser: typescriptParser,
                sourceType: 'module',
            },
        },
        rules: {
            'vue/component-name-in-template-casing': ['error', 'PascalCase', {
                registeredComponentsOnly: false,
            }],
        },
    },
];
