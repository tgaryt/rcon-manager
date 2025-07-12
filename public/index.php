<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Source RCON Interface</title>
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
				<h1 class="text-xl font-bold text-white">Source RCON Interface</h1>
				<a href="admin.php" class="bg-primary hover:bg-red-600 text-white px-4 py-2 rounded transition-colors">
					Admin Panel
				</a>
			</div>
		</div>
	</nav>

	<div class="container mx-auto px-4 py-8 max-w-7xl">
		<div class="bg-darker rounded-lg shadow-xl">
			<div class="bg-primary text-white px-6 py-4 rounded-t-lg">
				<h2 class="text-2xl font-bold flex items-center">
					<svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
					</svg>
					Source RCON Interface
				</h2>
			</div>
			
			<div class="p-6">
				<form id="rconForm" class="space-y-6">
					<div>
						<label for="command" class="block text-sm font-medium text-gray-300 mb-2">
							<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3"></path>
							</svg>
							RCON Command
						</label>
						<input type="text" id="command" class="w-full bg-gray-700 border border-gray-600 text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Enter RCON Command" required>
					</div>

					<div>
						<div class="flex justify-between items-center mb-2">
							<label for="servers" class="block text-sm font-medium text-gray-300">
								<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v4a2 2 0 01-2 2H5z"></path>
								</svg>
								Select Servers
							</label>
							<div class="flex items-center space-x-4">
								<span id="totalServers" class="text-sm text-gray-400">Total Servers: 0</span>
								<button type="button" id="selectAll" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm transition-colors">
									Select All
								</button>
							</div>
						</div>
						<select multiple id="servers" class="w-full bg-gray-700 border border-gray-600 text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" size="8" required>
							<option disabled class="text-gray-400">Loading servers...</option>
						</select>
					</div>

					<button type="submit" class="w-full bg-secondary hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
						<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
						</svg>
						Send Command
					</button>
				</form>
			</div>
		</div>

		<div id="responseArea" class="mt-8"></div>
	</div>

	<script src="assets/js/main.js"></script>
</body>
</html>
