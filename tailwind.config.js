/** @type {import('tailwindcss').Config} */
module.exports = {
  corePlugins: { preflight: false },
  content: [
    "./public/**/*.php",
    "./resources/**/*.php",
    "./modules/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        brand: '#0072ff',
        accent: '#00d2ff',
        dark: '#0e0e12',
        background: '#0B0F19',
        card: '#1A2235',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        display: ['Bebas Neue', 'cursive']
      }
    },
  },
  plugins: [],
}
