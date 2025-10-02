#!/bin/bash

BASE_URL="http://localhost:8000/api"

echo "=== Testando health Laravel ==="
curl -s $BASE_URL/health | jq || echo '{}'
echo -e "\n\n"

echo "=== Criando usuários ==="
id1=$(curl -s -X POST $BASE_URL/users \
    -H "Content-Type: application/json" \
    -d '{"name":"Fabio Salles","email":"fabio@example.com"}' | jq -r '.id // empty')

id2=$(curl -s -X POST $BASE_URL/users \
    -H "Content-Type: application/json" \
    -d '{"name":"Maria Silva","email":"maria@example.com"}' | jq -r '.id // empty')

echo "Usuário 1 criado com ID: ${id1:-"não criado"}"
echo "Usuário 2 criado com ID: ${id2:-"não criado"}"
echo -e "\n\n"

echo "=== Listando usuários ==="
curl -s $BASE_URL/users | jq || echo '{}'
echo -e "\n\n"

if [ -n "$id1" ]; then
    echo "=== Mostrando usuário ID $id1 ==="
    curl -s $BASE_URL/users/$id1 | jq || echo '{}'
    echo -e "\n\n"

    echo "=== Atualizando usuário ID $id1 ==="
    curl -s -X PUT $BASE_URL/users/$id1 \
        -H "Content-Type: application/json" \
        -d '{"name":"Fabio S. Atualizado","email":"fabio.s@example.com"}' | jq || echo '{}'
    echo -e "\n\n"
fi

if [ -n "$id2" ]; then
    echo "=== Deletando usuário ID $id2 ==="
    curl -s -X DELETE $BASE_URL/users/$id2 | jq || echo '{}'
    echo -e "\n\n"
fi

echo "=== Testando consumo do microserviço Node.js ==="
curl -s $BASE_URL/external | jq || echo '{}'
echo -e "\n\n"

echo "=== Testes finalizados 🚀 ==="
