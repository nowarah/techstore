FROM node:20-alpine AS base
WORKDIR /app

# ------- deps -------
FROM base AS deps
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci

# ------- build -------
FROM base AS build
COPY --from=deps /app/node_modules ./node_modules
COPY frontend/ .
RUN npm run build

# ------- production -------
FROM base AS production
ENV NODE_ENV=production

RUN addgroup --system --gid 1001 nodejs \
    && adduser --system --uid 1001 nextjs

COPY --from=build /app/public ./public
COPY --from=build --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=build --chown=nextjs:nodejs /app/.next/static ./.next/static

USER nextjs
EXPOSE 3000
ENV PORT=3000 HOSTNAME="0.0.0.0"
CMD ["node", "server.js"]
