FROM node:22-alpine

WORKDIR /app

# Copy package management files
COPY package*.json ./

# Install only production dependencies
RUN npm ci --only=production

# Copy application files
COPY . .

# Expose port (configured as 6969 in Laravel .env)
EXPOSE 6969

# Start application
CMD ["npm", "start"]
