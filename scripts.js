// Scripts for index.html (Login Page)
if (document.getElementById('login-form')) {
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('http://localhost:5500/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ correo: email, contraseña: password }),
            });

            if (response.ok) {
                const data = await response.json();
                localStorage.setItem('token', data.access_token);
                window.location.href = 'dashboard.html';
            } else {
                document.getElementById('error-message').textContent = 'Su Usuario o Contraseña son Incorrectos';
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('error-message').textContent = 'Error al iniciar sesión';
        }
    });
}

// Scripts for dashboard.html (Dashboard Page)
if (document.getElementById('logout-button')) {
    document.getElementById('logout-button').addEventListener('click', () => {
        localStorage.removeItem('token');
        window.location.href = 'index.html';
    });

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = 'index.html';
            return;
        }

        try {
            const response = await fetch('http://localhost:8000/auth/protected', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            });

            if (response.ok) {
                const data = await response.json();
                const userRole = data.user_role;
                loadMenu(userRole);
            } else {
                window.location.href = 'index.html';
            }
        } catch (error) {
            console.error('Error:', error);
            window.location.href = 'index.html';
        }
    });

    async function loadMenu(userRole) {
        const menu = document.getElementById('menu');
        const tables = ['usuarios', 'citas', 'especialidades', 'medico', 'pacientes'];
        tables.forEach(table => {
            const li = document.createElement('li');
            li.textContent = table;
            li.addEventListener('click', () => loadTableData(table, userRole));
            menu.appendChild(li);
        });
    }

    async function loadTableData(table, userRole) {
        const token = localStorage.getItem('token');
        try {
            const response = await fetch(`http://localhost:8000/api/${table}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            });

            if (response.ok) {
                const data = await response.json();
                displayTableData(table, data, userRole);
            } else {
                console.error('Error fetching data:', response.statusText);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function displayTableData(table, data, userRole) {
        const content = document.getElementById('content');
        content.innerHTML = '';

        const tableElement = document.createElement('table');
        const thead = document.createElement('thead');
        const tbody = document.createElement('tbody');

        const headers = Object.keys(data[0] || {});
        const trHead = document.createElement('tr');
        headers.forEach(header => {
            const th = document.createElement('th');
            th.textContent = header;
            trHead.appendChild(th);
        });
        if (userRole === 'admin') {
            const thActions = document.createElement('th');
            thActions.textContent = 'Actions';
            trHead.appendChild(thActions);
        }
        thead.appendChild(trHead);

        data.forEach(row => {
            const tr = document.createElement('tr');
            headers.forEach(header => {
                const td = document.createElement('td');
                td.textContent = row[header];
                tr.appendChild(td);
            });
            if (userRole === 'admin') {
                const tdActions = document.createElement('td');
                const editButton = document.createElement('button');
                editButton.textContent = 'Edit';
                editButton.addEventListener('click', () => editRecord(table, row));
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.addEventListener('click', () => deleteRecord(table, row.id));
                tdActions.appendChild(editButton);
                tdActions.appendChild(deleteButton);
                tr.appendChild(tdActions);
            }
            tbody.appendChild(tr);
        });

        tableElement.appendChild(thead);
        tableElement.appendChild(tbody);
        content.appendChild(tableElement);
    }

    async function editRecord(table, record) {
        // Implementar la lógica para editar un registro
    }

    async function deleteRecord(table, id) {
        const token = localStorage.getItem('token');
        try {
            const response = await fetch(`http://localhost:8000/api/${table}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            });

            if (response.ok) {
                loadTableData(table, 'admin');
            } else {
                console.error('Error deleting data:', response.statusText);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}
