<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard - Source RCON</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					colors: {
						primary: '#e04e4e',
						secondary: '#ffb500',
						dark: '#2c2f33',
						darker: '#23272a'
					}
				}
			}
		}
	</script>
</head>
<body class="bg-dark text-gray-100 min-h-screen">
	<nav class="bg-gray-900 shadow-lg">
		<div class="max-w-7xl mx-auto px-4">
			<div class="flex justify-between items-center py-4">
				<h1 class="text-xl font-bold text-white">Admin Dashboard</h1>
				<a href="index.php" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded transition-colors">
					Dashboard
				</a>
			</div>
		</div>
	</nav>

	<div class="container mx-auto px-4 py-8 max-w-7xl">
		<div id="alertArea"></div>

		<div class="bg-darker rounded-lg shadow-xl">
			<div class="bg-primary text-white px-6 py-4 rounded-t-lg">
				<h2 class="text-2xl font-bold">Manage Servers</h2>
			</div>
			
			<div class="p-6">
				<div class="grid md:grid-cols-2 gap-8">
					<div>
						<h3 class="text-xl font-semibold mb-4 text-white">Add New Server</h3>
						<form id="addServerForm" class="space-y-4">
							<div>
								<label for="ip" class="block text-sm font-medium text-gray-300 mb-1">IP Address</label>
								<input type="text" id="ip" name="ip" class="w-full bg-gray-700 border border-gray-600 text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="127.0.0.1" required>
							</div>
							
							<div>
								<label for="port" class="block text-sm font-medium text-gray-300 mb-1">Port</label>
								<input type="number" id="port" name="port" min="1" max="65535" class="w-full bg-gray-700 border border-gray-600 text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="27015" required>
							</div>
							
							<div>
								<label for="rcon_password" class="block text-sm font-medium text-gray-300 mb-1">RCON Password (Optional)</label>
								<input type="password" id="rcon_password" name="rcon_password" class="w-full bg-gray-700 border border-gray-600 text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Leave empty to use default">
							</div>
							
							<button type="submit" class="w-full bg-secondary hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors">
								Add Server
							</button>
						</form>
					</div>

					<div>
						<h3 class="text-xl font-semibold mb-4 text-white">Server List</h3>
						<div id="serverList" class="space-y-3">
							<div class="text-gray-400 text-center py-8">Loading servers...</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="assets/js/admin.js"></script>
</body>
</html>
