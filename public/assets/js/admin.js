class AdminInterface {
	constructor() {
		this.servers = [];
		this.init();
	}

	async init() {
		await this.loadServers();
		this.bindEvents();
	}

	async loadServers() {
		try {
			const response = await fetch('/api/servers.php');
			const data = await response.json();
			
			if (data.error) {
				this.showAlert(data.error, 'error');
				return;
			}

			this.servers = data;
			this.renderServerList();
		} catch (error) {
			console.error('Error loading servers:', error);
			this.showAlert('Failed to load servers', 'error');
		}
	}

	renderServerList() {
		const serverList = document.getElementById('serverList');
		
		if (this.servers.length === 0) {
			serverList.innerHTML = '<div class="text-gray-400 text-center py-8">No servers found</div>';
			return;
		}

		serverList.innerHTML = this.servers.map(server => this.createServerCard(server)).join('');
	}

	createServerCard(server) {
		return `
			<div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
				<div class="flex justify-between items-start">
					<div>
						<h4 class="font-medium text-white flex items-center space-x-2">
							<span>${server.ip}:${server.port}</span>
							${server.status ? `<span class="inline-block px-2 py-1 text-xs rounded ${server.status === 'online' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'}">${server.status}</span>` : ''}
						</h4>
						<p class="text-sm text-gray-400">${server.name || 'Unknown Server'}</p>
					</div>
					<div class="flex space-x-2">
						<button onclick="adminInterface.showPasswordModal(${server.id})" class="bg-yellow-600 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm transition-colors">
							Change Password
						</button>
						<button onclick="adminInterface.deleteServer(${server.id})" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-sm transition-colors">
							Delete
						</button>
					</div>
				</div>
			</div>
		`;
	}

	bindEvents() {
		document.getElementById('addServerForm').addEventListener('submit', this.handleAddServer.bind(this));
	}

	async handleAddServer(e) {
		e.preventDefault();

		const formData = new FormData(e.target);
		const serverData = {
			ip: formData.get('ip').trim(),
			port: parseInt(formData.get('port')),
			rcon_password: formData.get('rcon_password').trim()
		};

		if (!serverData.ip || !serverData.port) {
			this.showAlert('IP address and port are required', 'error');
			return;
		}

		try {
			const response = await fetch('/api/servers.php?action=add', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(serverData)
			});

			const data = await response.json();

			if (data.error) {
				this.showAlert(data.error, 'error');
				return;
			}

			this.showAlert(data.message || 'Server added successfully', 'success');
			e.target.reset();
			await this.loadServers();
		} catch (error) {
			console.error('Error adding server:', error);
			this.showAlert('Failed to add server', 'error');
		}
	}

	showPasswordModal(serverId) {
		const modal = document.createElement('div');
		modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
		modal.innerHTML = `
			<div class="bg-darker rounded-lg p-6 w-96 max-w-90vw">
				<h3 class="text-lg font-semibold text-white mb-4">Change RCON Password</h3>
				<form id="passwordForm">
					<div class="mb-4">
						<label for="newPassword" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
						<input type="password" id="newPassword" class="w-full bg-gray-700 border border-gray-600 text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
					</div>
					<div class="flex space-x-3">
						<button type="submit" class="flex-1 bg-secondary hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded transition-colors">
							Update
						</button>
						<button type="button" onclick="this.closest('.fixed').remove()" class="flex-1 bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded transition-colors">
							Cancel
						</button>
					</div>
				</form>
			</div>
		`;

		modal.querySelector('#passwordForm').addEventListener('submit', async (e) => {
			e.preventDefault();
			const newPassword = e.target.newPassword.value.trim();
			
			if (!newPassword) {
				this.showAlert('Password is required', 'error');
				return;
			}

			await this.updateRconPassword(serverId, newPassword);
			modal.remove();
		});

		document.body.appendChild(modal);
	}

	async updateRconPassword(serverId, newPassword) {
		try {
			const response = await fetch('/api/servers.php?action=update_password', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					server_id: serverId,
					new_rcon_password: newPassword
				})
			});

			const data = await response.json();

			if (data.error) {
				this.showAlert(data.error, 'error');
				return;
			}

			this.showAlert(data.message || 'Password updated successfully', 'success');
		} catch (error) {
			console.error('Error updating password:', error);
			this.showAlert('Failed to update password', 'error');
		}
	}

	async deleteServer(serverId) {
		if (!confirm('Are you sure you want to delete this server?')) {
			return;
		}

		try {
			const response = await fetch('/api/servers.php?action=delete', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					server_id: serverId
				})
			});

			const data = await response.json();

			if (data.error) {
				this.showAlert(data.error, 'error');
				return;
			}

			this.showAlert(data.message || 'Server deleted successfully', 'success');
			await this.loadServers();
		} catch (error) {
			console.error('Error deleting server:', error);
			this.showAlert('Failed to delete server', 'error');
		}
	}

	showAlert(message, type = 'info') {
		const colors = {
			success: 'bg-green-900 border-green-500 text-green-100',
			error: 'bg-red-900 border-red-500 text-red-100',
			info: 'bg-blue-900 border-blue-500 text-blue-100'
		};

		const alert = document.createElement('div');
		alert.className = `${colors[type]} border rounded-lg p-4 mb-4 fixed top-4 right-4 z-40 min-w-80 shadow-lg`;
		alert.innerHTML = `
			<div class="flex justify-between items-center">
				<span>${message}</span>
				<button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-xl">&times;</button>
			</div>
		`;

		document.body.appendChild(alert);

		setTimeout(() => {
			if (alert.parentElement) {
				alert.remove();
			}
		}, 5000);
	}
}

document.addEventListener('DOMContentLoaded', () => {
	window.adminInterface = new AdminInterface();
});
