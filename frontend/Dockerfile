# Multi-stage build for Nuxt 3 application
FROM node:20-alpine AS base

# Install dependencies only when needed
FROM base AS deps
RUN apk add --no-cache libc6-compat
WORKDIR /app

# Copy package files
COPY src/package*.json ./
RUN npm ci --only=production && npm cache clean --force

# Build stage
FROM base AS builder
WORKDIR /app
COPY src/package*.json ./
RUN npm ci

# Copy source code
COPY src/ .

# Build the application
RUN npm run build

# Production stage
FROM base AS runner
WORKDIR /app

# Don't run production as root
RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nuxtjs

# Copy built application
COPY --from=builder --chown=nuxtjs:nodejs /app/.output /app/.output

USER nuxtjs

# Expose port
EXPOSE 3000

ENV PORT 3000
ENV HOST 0.0.0.0

# Start the application
CMD ["node", "/app/.output/server/index.mjs"]