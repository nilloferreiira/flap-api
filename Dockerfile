# 1. BUILD STAGE: Imagem temporária para instalar dependências
FROM composer:2.6 AS composer_build

WORKDIR /app

# Copia os arquivos de configuração do Composer
COPY composer.json composer.lock ./

# Roda o Composer Install **sem** dependências de desenvolvimento
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 2. APP STAGE: Imagem final de produção
FROM php:8.0-fpm AS app_production

# Define o diretório de trabalho
WORKDIR /var/www

# Instala ferramentas essenciais
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala as extensões PHP necessárias para o Laravel
RUN docker-php-ext-install pdo_mysql zip

# Copia as dependências instaladas do Composer Stage
COPY --from=composer_build /app/vendor /var/www/vendor

# Copia o código da aplicação
COPY . .

# Cria um usuário não-root (boa prática de segurança)
RUN usermod -u 1000 www-data

# Define permissões
# Garante que o PHP (usuário www-data) possa escrever nas pastas críticas
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Altera para o usuário www-data para segurança
USER www-data

# Expõe a porta do PHP-FPM
EXPOSE 9000

# Comando para iniciar o PHP-FPM
CMD ["php-fpm"]