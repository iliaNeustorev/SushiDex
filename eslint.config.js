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
            'vue/attribute-hyphenation': ['error', 'always'],
            'vue/first-attribute-linebreak': ['error', {
                singleline: 'ignore',
                multiline: 'below',
            }],
            'vue/html-closing-bracket-newline': ['error', {
                singleline: 'never',
                multiline: 'always',
            }],
            'vue/html-closing-bracket-spacing': ['error', {
                startTag: 'never',
                endTag: 'never',
                selfClosingTag: 'always',
            }],
            'vue/html-indent': ['error', 4, {
                attribute: 1,
                baseIndent: 1,
                closeBracket: 0,
                alignAttributesVertically: false,
            }],
            'vue/html-quotes': ['error', 'double', {
                avoidEscape: false,
            }],
            'vue/max-attributes-per-line': ['error', {
                singleline: {
                    max: 3,
                },
                multiline: {
                    max: 1,
                },
            }],
            'vue/mustache-interpolation-spacing': ['error', 'always'],
        },
    },
];
