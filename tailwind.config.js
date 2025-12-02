import tailwindcss from '@tailwindcss/vite'

export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#6366f1", // warna indigo default
        secondary: "#f9fafb",
        accent: "#ec4899",
      },
    },
  },
  plugins: [
    tailwindcss(),
  ],
}
