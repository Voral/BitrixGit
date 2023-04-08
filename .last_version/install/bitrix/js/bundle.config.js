const isProd = process.argv.includes('--prod');
module.exports = {
    input: './src/application.js',
    output: './dist/vasoft-git.bundle.js',
    namespace: 'BX.Vasoft',
    browserslist: true,
    minification: isProd,
    sourceMaps: !isProd,
    resolveFilesImport: {
        output: './dist/',
        include: ['**/*.svg', '**/*.png'],
        exclude: []
    },
    cssImages: {
        type: 'copy',
        output: './dist/',
        maxSize: 14,
        svgo: true
    }
};