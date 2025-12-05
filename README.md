# Flap API

API para o sistema Flap — serviço backend para um quadro Kanban com usuários, cargos, clientes, listas (colunas) e jobs (cards), incluindo elementos de tarefa como comentários, links, membros e checklists.

Este README resume como instalar, executar e desenvolver localmente, e traz um resumo das principais funcionalidades (baseado no manual do usuário).

## Conteúdo rápido

- Visão geral do sistema
- Requisitos
- Instalação e execução (local / Docker)
- Seeders importantes
- Principais fluxos (Administrador e Usuário)
- Desenvolvimento e testes

## Visão geral

O Flap API expõe endpoints para gerenciar autenticação, usuários, cargos (roles), permissões, clientes, listas (kanban) e tarefas (jobs). Cada tarefa pode ter comentários, links, membros e checklists com itens.

O projeto segue convenções Laravel e está organizado em pastas típicas (`app/Models`, `app/Http/Controllers`, `app/Services`, `database/migrations`, `database/seeders`, etc.).

## Requisitos

- PHP 8.x compatível com Laravel 8
- Composer
- Node.js + npm
- Banco de dados MySQL

Ou, via Docker Compose (recomendado):

## Instalação (ambiente local)

1. Clone o repositório

   git clone <repo>
   cd flap-api

2. Instale dependências PHP

   composer install

3. Copie o arquivo de ambiente e gere a chave

   cp .env.example .env
   php artisan key:generate

4. Configure o `.env` (DB, cache, mail, etc.)

5. Rode migrações e seeders importantes

   php artisan migrate
   php artisan db:seed --class=PermissionSeeder

6. (Opcional) Instale dependências JS e rode assets

   npm install
   npm run dev

7. Inicie o servidor local

   php artisan serve

Ou, usando Docker Compose (quando disponível no repositório):

# sobe os serviços em background e (re)constrói imagens

docker compose up -d --build

Depois que os serviços estiverem rodando, execute as migrations e os seeders dentro do container da aplicação. Exemplo:

# execute as migrations + seed dentro do container (o nome do container pode variar)

sudo docker exec -it flap-api-app-1 php artisan migrate --seed

Notas importantes:

- Ajuste `flap-api-app-1` para o nome real do container da aplicação — verifique com `docker compose ps`.
- Se seu usuário pertence ao grupo `docker`, o `sudo` pode não ser necessário; use apenas `docker exec -it <container> ...`.
- Aguarde o banco de dados estar pronto antes de rodar as migrations. Você pode acompanhar logs com `docker compose logs -f` ou checar o status com `docker compose ps`.
- Se preferir rodar apenas as migrations sem seed: `php artisan migrate`.
- Não esqueça de ajustar as variáveis de ambiente (`.env`) para apontar para os serviços Docker (DB, redis, etc.) se necessário.

## Seeders importantes

- `PermissionSeeder` — popula permissões do sistema (jobs, usuários, clientes, papéis, listas, etc.).

Execute sempre após as migrações em um ambiente novo para garantir as permissões básicas.

## Principais fluxos (resumo do manual do usuário)

Visão Administrador

- Gerenciar usuários: criar, editar, excluir.
- Gerenciar cargos (roles) e permissões: definir permissões por cargo.
- Gerenciar clientes.
- Gerenciar listas do Kanban: criar/editar/excluir colunas.
- Gerenciar jobs: criar/editar/excluir/mover/arquivar cards.

Visão Usuário Comum

- Visualizar o quadro Kanban e os detalhes dos jobs.
- Mover jobs entre listas (se o cargo permitir).
- Atualizar senha e perfil.

Elementos de tarefa

- Comentários: criar/editar/excluir comentários em um job.
- Links: anexar URLs ao job.
- Membros: adicionar/remover usuários ao job (task members).
- Checklists: criar checklists com vários itens; é possível criar, atualizar e deletar itens. Observe que o projeto usa soft deletes — ver seção de comportamento abaixo.

## Soft deletes e cascata

Importante: o `ON DELETE CASCADE` no banco NÃO é acionado quando você usa soft deletes (Eloquent `SoftDeletes`).
Por isso o código do projeto trata explicitamente a sincronização/remoção de itens relacionados (por exemplo checklists e seus itens) usando eventos (`deleting`, `restoring`) e/ou métodos de sincronização em transação (`syncItems`).

Se você precisar remover itens fisicamente ao fazer `forceDelete`, há lógica para propagar `forceDelete` para os relacionamentos onde aplicável.

## Mais informações

- Rotas da API: ver `routes/api.php`.
- Regras de validação: muitos endpoints usam FormRequests em `app/Http/Requests`.
- Serviços: lógica de negócio fica em `app/Services`.
