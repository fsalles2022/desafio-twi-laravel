README completo (README.md)
# 🚀 Desafio Laravel - API de Usuários

API em **Laravel 11** para gerenciar usuários e consumir um microserviço externo.

---

## ✨ Funcionalidades

- ✅ Health check da API (`/api/health`)  
- ✅ CRUD completo de usuários (`/api/users`)  
- ✅ Consumo de microserviço externo (`/api/external`)  

---

## 🛠 Requisitos

- PHP >= 8.2  
- Composer  
- Laravel 11  
- MySQL  
- `jq` (opcional, para testes via script)  

---

## 📦 Instalação

```bash
git clone <URL_DO_REPOSITORIO>
cd desafio-laravel
composer install
cp .env.example .env
php artisan key:generate


Configure o banco de dados no .env: copiar .env.example.

Rode as migrations:

php artisan migrate

▶️ Executando a API
php artisan serve


A API estará disponível em:

http://localhost:8000

🔗 Rotas principais
Método	Endpoint	Descrição
GET	/api/health	Health check da API
GET	/api/users	Listar todos os usuários
POST /api/users	Criar novo usuário
GET	/api/users/{id}	Mostrar usuário específico
PUT	/api/users/{id}	Atualizar usuário
DELETE	/api/users/{id}	Deletar usuário
GET	/api/external	Consumir microserviço externo

Todas as rotas usam o prefixo /api.

🧪 Testando com script
bash test.sh


O script cria, lista, atualiza e deleta usuários, além de testar o microserviço externo.

✅ Observações

O microserviço externo deve estar ativo em http://localhost:8001/health.

Delete retorna 204 No Content quando bem-sucedido.

Todas as rotas usam prefixo / api.