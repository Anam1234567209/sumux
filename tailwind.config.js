/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      width: {
        450: '1800px',
        550: '2200px',
      },
      minWidth: {
        450: '1800px',
        550: '2200px',
      },
    },
  },
  plugins: [],
};
