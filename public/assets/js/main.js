class RconInterface {
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
			this.renderServerOptions();
			this.updateServerCount();
		} catch (error) {
			console.error('Error loading servers:', error);
			this.showAlert('Failed to load servers', 'error');
		}
	}

	renderServerOptions() {
		const serverSelect = document.getElementById('servers');
		
		if (this.servers.length === 0) {
			serverSelect.innerHTML = '<option disabled class="text-gray-400">No servers available</option>';
			return;
		}

		serverSelect.innerHTML = this.servers.map(server => 
			`<option value="${server.id}" class="py-2">${server.name}</option>`
		).join('');
	}

	updateServerCount() {
		document.getElementById('totalServers').textContent = `Total Servers: ${this.servers.length}`;
	}

	bindEvents() {
		document.getElementById('selectAll').addEventListener('click', this.toggleSelectAll.bind(this));
		document.getElementById('rconForm').addEventListener('submit', this.handleFormSubmit.bind(this));
	}

	toggleSelectAll() {
		const serverSelect = document.getElementById('servers');
		const button = document.getElementById('selectAll');
		const selectedCount = serverSelect.selectedOptions.length;
		const totalCount = serverSelect.options.length;

		if (selectedCount === totalCount) {
			for (let option of serverSelect.options) {
				option.selected = false;
			}
			button.textContent = 'Select All';
		} else {
			for (let option of serverSelect.options) {
				option.selected = true;
			}
			button.textContent = 'Deselect All';
		}
	}

	async handleFormSubmit(e) {
		e.preventDefault();

		const command = document.getElementById('command').value.trim();
		const serverSelect = document.getElementById('servers');
		const selectedServers = Array.from(serverSelect.selectedOptions).map(option => parseInt(option.value));

		if (!command) {
			this.showAlert('Please enter a command', 'error');
			return;
		}

		if (selectedServers.length === 0) {
			this.showAlert('Please select at least one server', 'error');
			return;
		}

		if (command.toLowerCase() === '_restart') {
			if (!confirm('You are about to send a "_restart" command. Are you sure?')) {
				return;
			}
		}

		await this.executeCommand(command, selectedServers);
	}

	async executeCommand(command, serverIds) {
		try {
			const response = await fetch('/api/rcon.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					command: command,
					server_ids: serverIds
				})
			});

			const data = await response.json();

			if (data.error) {
				this.showAlert(data.error, 'error');
				return;
			}

			this.showAlert(`Command "${command}" sent successfully to ${serverIds.length} server(s)`, 'success');
			this.displayResults(data);
		} catch (error) {
			console.error('Error executing command:', error);
			this.showAlert('Failed to execute command', 'error');
		}
	}

	displayResults(responses) {
		const responseArea = document.getElementById('responseArea');
		responseArea.innerHTML = '';

		const successResponses = responses.filter(r => r.success);
		const errorResponses = responses.filter(r => !r.success);

		if (successResponses.length > 0) {
			const successContainer = this.createResultContainer('Success', 'bg-green-900 border-green-500', successResponses);
			responseArea.appendChild(successContainer);
		}

		if (errorResponses.length > 0) {
			const errorContainer = this.createResultContainer('Errors', 'bg-red-900 border-red-500', errorResponses);
			responseArea.appendChild(errorContainer);
		}
	}

	createResultContainer(title, classes, responses) {
		const container = document.createElement('div');
		container.className = `${classes} border rounded-lg p-4 mb-4`;
		
		container.innerHTML = `
			<h3 class="text-lg font-semibold mb-3 text-white">${title}</h3>
			${responses.map(response => this.createResponseBlock(response)).join('')}
		`;

		return container;
	}

	createResponseBlock(response) {
		const responseId = 'response_' + Math.random().toString(36).substr(2, 9);
		
		return `
			<div class="bg-darker rounded p-4 mb-3 border border-gray-600">
				<div class="flex justify-between items-start mb-2">
					<h4 class="font-medium text-white">${response.server}</h4>
					<button onclick="rconInterface.copyToClipboard('${responseId}')" class="bg-gray-600 hover:bg-gray-500 text-white px-2 py-1 rounded text-xs transition-colors">
						Copy
					</button>
				</div>
				<pre id="${responseId}" class="bg-gray-800 p-3 rounded text-sm text-gray-100 whitespace-pre-wrap overflow-x-auto">${response.response}</pre>
			</div>
		`;
	}

	async copyToClipboard(elementId) {
		const element = document.getElementById(elementId);
		const text = element.textContent;

		try {
			await navigator.clipboard.writeText(text);
			this.showAlert('Copied to clipboard!', 'success');
		} catch (error) {
			console.error('Failed to copy:', error);
			this.showAlert('Failed to copy to clipboard', 'error');
		}
	}

	showAlert(message, type = 'info') {
		const colors = {
			success: 'bg-green-900 border-green-500 text-green-100',
			error: 'bg-red-900 border-red-500 text-red-100',
			info: 'bg-blue-900 border-blue-500 text-blue-100'
		};

		const alert = document.createElement('div');
		alert.className = `${colors[type]} border rounded-lg p-4 mb-4 fixed top-4 right-4 z-50 min-w-80 shadow-lg`;
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
	window.rconInterface = new RconInterface();
});
