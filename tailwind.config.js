/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            fontFamily: {
                inter: ['Inter', 'system-ui', 'sans-serif'],
            },
            colors: {
                surface:       '#F8FAFC',
                'on-surface':  '#0F172A',
                primary:       '#1E293B',
                accent:        '#6366F1',
                border:        '#E2E8F0',
            },
            boxShadow: {
                card: '0 1px 3px rgba(15, 23, 42, 0.06)',
                auth: '0 4px 24px rgba(15, 23, 42, 0.08)',
            },
        },
    },
    plugins: [],
};
