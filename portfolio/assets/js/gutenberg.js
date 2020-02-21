/**
 * Adds custom section component
 * to Gutenberg editor
 */

const { InnerBlocks } = wp.editor;

wp.blocks.registerBlockType( 'portfolio/shadowbox', {
    title: 'Shadow Box' ,
    icon: 'editor-insertmore',
    category: 'common',
    keywords: [
        'shadow',
        'box'
    ],

    edit( { className } ) {
        return (
            React.createElement(
                'div',
                {
                    className: 'ku-portfolio-shadowbox',
                },
                React.createElement(
                    InnerBlocks,
                    {
                        allowedBlocks: ['core/heading', 'core/paragraph', 'core/shortcode', 'core/image', 'core/columns', 'core/spacer', 'core/button'],
                    }
                )
            )
        );
    },

    save() {
        return (
            React.createElement(
                'div',
                {
                    className: 'shadowbox',
                },
                React.createElement(
                    InnerBlocks.Content
                )
            )
        );
    }
});

