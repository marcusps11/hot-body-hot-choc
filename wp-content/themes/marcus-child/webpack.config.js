const webpack = require('webpack')
const path = require('path')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const HtmlWebpackPlugin = require('html-webpack-plugin');

const entry = [path.resolve(__dirname, 'scripts/app.js'), path.resolve(__dirname, 'styles/main.scss')];

const config = {
  entry: entry,
  output: {
    filename: 'bundle.js',
    path: path.resolve(__dirname, './build'),
    publicPath: '/build'
  },

  devtool: 'source-map',
  module: {
    rules: [{
      test: /\.css$/,
      use: ExtractTextPlugin.extract({
        use: ['css-loader?importLoaders=1']
      })},
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract(['css-loader', 'sass-loader'])
      },
      {
        exclude: /node_modules/,
        test: /\.js?/,
        use: 'babel-loader',
        include: path.resolve(__dirname, 'src/scripts')
      }
    ]
  },
  plugins: [
    new ExtractTextPlugin({
      filename: '../style.css',
      allChunks: true
    })
  ]
}
module.exports = config;
