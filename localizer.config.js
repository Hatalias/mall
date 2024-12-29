
export default {
    root: '.',
    defaultLocale: 'en',
    namespace: 'hts.mall',
    localeDir: 'lang',
    files: [
        '**/*.php',
        '**/*.htm',
        '**/*.html',
        '**/*.yaml',
        '!assets/**/*',
        '!lang/**/*',
        '!node_modules/**/*',
        '!tests/**/*',
        '!updates/*',
        '!vendor/**/*',
    ],
    theme: {
        name: 'Hts/Mall Translations',
        logo: 'assets/images/orders-icon.svg',
    },
    server: {
        port: 3005,
    }
};