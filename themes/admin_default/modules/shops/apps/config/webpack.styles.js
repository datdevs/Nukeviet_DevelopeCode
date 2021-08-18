const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const helpers = require('./helpers')

const isDev = process.env.NODE_ENV === 'development'

module.exports = {
  module: {
    rules: [
      {
        test: /\.((c|sa|sc)ss)$/i,
        use: [
          isDev ? 'vue-style-loader' : MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              importLoaders: 2,
              sourceMap: isDev,
              modules: {
                localIdentName: isDev ? '[name]__[local]' : '[hash:base64:5]',
              },
            },
          },
          { loader: 'postcss-loader', options: { sourceMap: isDev } },
          { loader: 'sass-loader', options: { sourceMap: isDev } },
        ],
        include: [helpers.pluginPath('../src/vue')],
      },
      {
        test: /\.((c|sa|sc)ss)$/i,
        use: [
          isDev ? 'vue-style-loader' : MiniCssExtractPlugin.loader,
          { loader: 'css-loader', options: { sourceMap: isDev } },
          { loader: 'postcss-loader', options: { sourceMap: isDev } },
          { loader: 'sass-loader', options: { sourceMap: isDev } },
        ],
        exclude: [helpers.pluginPath('../src/vue')],
      },
    ],
  },
}
