# TBD, This is just a draft, due to hardware limitation
FROM debian:trixie-backports

# Copy only the composer files first to leverage caching
COPY composer.json composer.lock ./

# Use a Docker cache mount to speed up subsequent image builds
RUN --mount=type=cache,target=~/.composer/cache \
    composer install --optimize-autoloader --no-dev --no-interaction --no-plugins --no-scripts

# Copy the rest of your application code afterwards
COPY . .

# STAGE 1: Build the assets (Needs Vite and devDependencies)
FROM node:24-alpine AS builder
WORKDIR /app
COPY package.json pnpm-lock.yaml ./
RUN --mount=type=cache,target=~/.local/share/pnpm/store pnpm install --frozen-lockfile
COPY . .
RUN pnpm build

# STAGE 2: Serve the assets (Production State - No Vite needed!)
FROM nginx:alpine
# Copy the compiled static production assets from Stage 1 into Nginx
COPY --from=builder /app/dist /usr/share/nginx/html
