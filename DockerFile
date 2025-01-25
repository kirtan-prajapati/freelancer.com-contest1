# Use the official PHP image as a base
FROM php:7.4-cli

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the current directory contents into the container
COPY . .

# Install dependencies (if needed)
RUN apt-get update && apt-get install -y libzip-dev \
    && docker-php-ext-install zip

# Expose port 10000 (or whatever port you're using)
EXPOSE 10000

# Command to run the PHP built-in server
CMD ["php", "-S", "0.0.0.0:10000"]
