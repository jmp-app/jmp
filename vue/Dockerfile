# build stage
FROM node:8-alpine as builder
WORKDIR /app
ARG API
COPY package*.json ./
RUN npm install
COPY . .
RUN cp jmp.config.js.example jmp.config.js & \
    sed -i -- "s|http://localhost/api|${API}|g" "jmp.config.js"

RUN npm run build

FROM nginx:alpine
COPY --from=builder /app/dist /var/www/public
COPY default.conf /etc/nginx/conf.d/default.conf
EXPOSE 8080
CMD ["nginx", "-g", "daemon off;"]
