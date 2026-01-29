const webpack = require('webpack');
const path = require('path');
// const { VueLoaderPlugin } = require('vue-loader'); // Import VueLoaderPlugin
let mix = require('laravel-mix');

mix.setPublicPath('assets');
mix.setResourceRoot('../');

mix.webpackConfig({
    module: {
        rules: [
            {
                test: /\.(js|vue)$/,
                exclude: /node_modules/,
                loader: 'eslint-loader',
                enforce: 'pre',
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            }
        ]
    },
    output: {
        publicPath: Mix.isUsing('hmr') ? '/' : path.resolve(__dirname + '/../assets'),
        chunkFilename: 'admin/js/[name].js'
    },
    plugins: [
        // new VueLoaderPlugin(), // Add VueLoaderPlugin here
        new webpack.IgnorePlugin({
            resourceRegExp: /^\.\/locale$/,
            contextRegExp: /moment$/
        })
    ],
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            '@': path.resolve(__dirname, '../'),
            'vue$': 'vue/dist/vue.esm.js'
        }
    }
});

mix.options({ processCssUrls: false });

module.exports = mix;
