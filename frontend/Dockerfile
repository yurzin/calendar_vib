FROM node:20-alpine

# Установка рабочей директории
WORKDIR /app

# Копирование package.json и package-lock.json
COPY package*.json ./

# Очистка кэша и установка зависимостей
RUN npm cache clean --force && \
    npm install

# Копирование остальных файлов
COPY . .

# Создание необходимых файлов конфигурации, если их нет
RUN if [ ! -f tailwind.config.js ]; then \
    echo "/** @type {import('tailwindcss').Config} */" > tailwind.config.js && \
    echo "export default {" >> tailwind.config.js && \
    echo "  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"]" >> tailwind.config.js && \
    echo "}" >> tailwind.config.js; \
    fi

RUN if [ ! -f postcss.config.js ]; then \
    echo "export default {" > postcss.config.js && \
    echo "  plugins: {" >> postcss.config.js && \
    echo "    tailwindcss: {}," >> postcss.config.js && \
    echo "    autoprefixer: {}" >> postcss.config.js && \
    echo "  }" >> postcss.config.js && \
    echo "}" >> postcss.config.js; \
    fi

# Обновление style.css
RUN echo "@tailwind base;" > src/style.css && \
    echo "@tailwind components;" >> src/style.css && \
    echo "@tailwind utilities;" >> src/style.css

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]