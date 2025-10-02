# TWI - Desafio Fullstack (Laravel + Node.js)

Este repositório contém dois projetos principais:

- **desafio-laravel/** → API desenvolvida em **Laravel 11** com autenticação Sanctum  
- **node-microservicos/** → Microserviços em **Node.js**  

---

## 📂 Estrutura do projeto
twi/
├── desafio-laravel/ # Projeto principal em Laravel
└── node-microservicos/ # Microserviços em Node.js

yaml
Copiar código

---

## 🚀 Como rodar o Laravel

### 1. Clonar o repositório
```bash
git clone https://github.com/fsalles2022/desafio-twi-laravel.git
cd twi/desafio-laravel
2. Instalar dependências
bash
Copiar código
composer install
3. Configurar variáveis de ambiente
Crie o arquivo .env a partir do .env.example:

bash
Copiar código
cp .env.example .env
Edite o .env conforme seu ambiente (banco de dados, mail, etc).

4. Gerar a key da aplicação
bash
Copiar código
php artisan key:generate
5. Rodar as migrations
bash
Copiar código
php artisan migrate
6. Subir servidor
bash
Copiar código
php artisan serve
App disponível em: http://127.0.0.1:8000

⚡ Como rodar os microserviços em Node.js
1. Acesse a pasta
bash
Copiar código
cd twi/node-microservicos
2. Instalar dependências
bash
Copiar código
npm install
3. Rodar em desenvolvimento
bash
Copiar código
npm run dev
📌 Postman Collection
As collections para testar os endpoints estão na pasta /postman.

desafio-laravel.postman_collection.json

desafio-laravel.postman_environment.json

🛠️ Tecnologias usadas
Backend
Laravel 11 (PHP 8.2+)

Sanctum (Autenticação)

Node.js (Microserviços)

Express.js

Banco de Dados
MySQL / MariaDB

Ferramentas
Docker / Docker Compose

Postman

🤝 Contribuição
Faça um fork do projeto

Crie uma branch (git checkout -b feature/nova-feature)

Commit suas mudanças (git commit -m 'feat: adiciona nova feature')

Faça push (git push origin feature/nova-feature)

Abra um Pull Request
