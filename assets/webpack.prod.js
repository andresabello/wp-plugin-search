const {merge} = require('webpack-merge')
const common = require('./webpack.common.js')
const TerserPlugin = require("terser-webpack-plugin")
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin')

module.exports = merge(common, {
    mode: 'production',
    optimization: {
        minimize: true,
        concatenateModules: true,
        minimizer: [
            new CssMinimizerPlugin(),
            new TerserPlugin({
                minify: TerserPlugin.swcMinify,
                extractComments: true,
            }),
        ],
    },
})