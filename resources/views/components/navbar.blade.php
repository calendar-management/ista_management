<div class="bg-sidebar text-white w-64 px-4 py-6 flex flex-col hidden md:block min-h-screen">
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-6">{{ ucfirst(auth()->user()->role) }}</h2>
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
            {{ $slot }}
            <li>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button href="{{ route('calendar') }}"
                        class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white w-full">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>

        </ul>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile sidebar toggle
        const sidebarToggle = document.querySelector('button.md\\:hidden');
        const sidebar = document.querySelector('.bg-sidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('hidden');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 768 && !sidebar.contains(event.target) && !sidebarToggle.contains(
                    event.target)) {
                sidebar.classList.add('hidden');
            }
        });

        // Animation for progress bars
        const progressBars = document.querySelectorAll('.progress-bar-fill');
        setTimeout(function() {
            progressBars.forEach(bar => {
                const targetWidth = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = targetWidth;
                }, 100);
            });
        }, 300);
    });
</script>