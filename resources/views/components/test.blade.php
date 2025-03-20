<div class="bg-sidebar text-white w-64 px-4 py-6 flex flex-col hidden md:block">
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-6">{{ ucfirst(auth()->user()->role)}}</h2>
        <div class="flex items-center space-x-3 mb-6">
            <div class="avatar w-10 h-10">
                <span class="text-white font-bold">{{ substr(auth()->user()->name ?? 'User', 0, 1) }}</span>
            </div>
            <div>
                <p class="text-sm font-medium">{{ auth()->user()->name ?? 'User Name' }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->email ?? 'email@example.com' }}</p>
            </div>
        </div>
    </div>
    <nav class="flex-1">
        <ul class="space-y-2">
            <li>
                <a href=""
                    class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#" class="sidebar-link active flex items-center px-4 py-2 text-white">
                    <i class="fas fa-users mr-3"></i>
                    <span>Groups</span>
                </a>
            </li>
            <li>
                <a href="#"
                    class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                    <i class="fas fa-book mr-3"></i>
                    <span>Modules</span>
                </a>
            </li>
            <li>
                <a href="#"
                    class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    <span>Calendar</span>
                </a>
            </li>
            <li>
                <a href="#"
                    class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Reports</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="mt-auto">
        <a href="#" class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
            <i class="fas fa-sign-out-alt mr-3"></i>
            <span>Logout</span>
        </a>
    </div>
</div>