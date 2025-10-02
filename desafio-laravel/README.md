# Desafio Laravel + Microserviço Node.js

Este repositório contém uma API em Laravel 11 para CRUD de usuários e um microserviço Node.js para health check.

---

## 🚀 Instruções de Setup

### 1. Clonar o repositório
``` 
git clone https://github.com/fsalles2022/desafio-twi-laravel.git
cd desafio-laravel

2. Instalar dependências do Laravel 
composer install

3. Configurar variáveis de ambiente
Copie o arquivo .env.example para .env:
cp .env.example .env

Atualize as variáveis:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=desafio
DB_USERNAME=seu_usuario
DB_PASSWORD=

4. Gerar chave da aplicação
php artisan key:generate

5. Rodar migrações
php artisan migrate

6. Iniciar o servidor Laravel
php artisan serve

A API estará disponível em:
👉 http://localhost:8000/api

▶️ Rodando o microserviço Node.js
Dentro da pasta microservice-node:

npm install
node index.js

O microserviço ficará acessível em:
👉 http://localhost:8001/health

📌 Endpoints da API
🔹 Health Check
 
 -------------
GET /api/health
✅ Retorna o status da aplicação Laravel.

Exemplo de resposta:

json
 -------------
{
  "status": "ok"
}

🔹 Usuários (CRUD)
GET /api/users → Lista todos os usuários.

POST /api/users → Cria novo usuário.
Body (JSON):

json
 -------------
{
  "name": "Nome do Usuário",
  "email": "email@example.com"
}

GET /api/users/{id} → Detalhes de um usuário.

PUT /api/users/{id} → Atualiza dados de um usuário.
Body (JSON):

json
 -------------
{
  "name": "Novo Nome",
  "email": "novoemail@example.com"
}
DELETE /api/users/{id} → Remove usuário.
Resposta: 204 No Content

🔹 Integração com Microserviço Node.js
sql
 -------------

GET /api/external
→ Consome o endpoint /health do microserviço Node.js.

Resposta quando ativo:

json
 -------------
{
  "status": "ok"
}

Resposta quando indisponível:

json
 -------------
{
  "status": "error",
  "message": "Microserviço indisponível"
}

🧪 Testes Automatizados (Shell Script)
O projeto possui um script test.sh para validar o fluxo completo.

Rodar testes:
 -------------
chmod +x test.sh
./test.sh

Ele executa:

Health Check

Criação de usuários

Listagem

Consulta por ID

Atualização

Exclusão

Consumo do microserviço Node.js

📄 Licença
Este projeto é apenas para fins de estudo/desafio.
