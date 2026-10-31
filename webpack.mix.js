let mix = require('laravel-mix');
const path = require("path");

mix.webpackConfig({
    module: {
        rules: [
            {
                enforce: 'pre',
                test: /\.mjs$/,
                loader: 'eslint-loader',
                exclude: /node_modules/
            }
        ]
    },
    output: {
        publicPath: Mix.isUsing('hmr') ? '/' : '/wp-content/plugins/quick-product-enquiry-for-woocommerce/assets/',
        chunkFilename: 'admin/js/[name].js'
    },
    plugins: [

    ],
    resolve: {
        extensions: ['.*', '.wasm', '.mjs', '.js', '.jsx', '.json', '.vue'],
        alias: {
            '@Pieces': path.resolve(__dirname, 'resources/admin/Pieces'),
            '@': path.resolve(__dirname, 'resources/admin')
        }
    }
});

mix.options({ processCssUrls: false });

mix.js('resources/admin/boot.js', 'assets/admin/js/boot.js').vue({ version: 2 });
mix.js('resources/admin/start.js', 'assets/admin/js/start.js').vue({ version: 2 });
mix.copy('resources/admin/images', 'assets/admin/images');
mix.sass('resources/scss/admin.scss', 'assets/admin/css/admin.css');
mix.copy('node_modules/element-ui/lib/theme-chalk/fonts', 'assets/admin/css/fonts');
mix.copy('resources/public/images', 'assets/public/images');

// Quick Product Enquiry for WooCommerce js
mix.js('resources/public/js/qpefw.js', 'assets/public/js/quick-product-enquiry-for-woocommerce.js');
mix.sass('resources/scss/public-qpefw.scss', 'assets/public/css/quick-product-enquiry-for-woocommerce.css');
