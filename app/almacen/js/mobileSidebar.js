(function(){
    function closeMobileSidebar(){
        var sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        document.body.classList.remove('mobile-sidebar-open');
        sidebar.classList.remove('mobile-open');
    }

    document.addEventListener('click', function(event){
        if (!document.body.classList.contains('mobile-sidebar-open')) return;
        var sidebar = document.querySelector('.sidebar');
        var sidebarToggleTop = document.getElementById('sidebarToggleTop');
        if (!sidebar) return;
        if (sidebar.contains(event.target)) return;
        if (sidebarToggleTop && sidebarToggleTop.contains(event.target)) return;
        closeMobileSidebar();
    });

    function attachMobileSidebarHandler() {
        var sidebarToggleTop = document.getElementById('sidebarToggleTop');
        var sidebar = document.querySelector('.sidebar');
        if (!sidebarToggleTop || !sidebar) return;
        sidebarToggleTop.addEventListener('click', function(event){
            if (window.innerWidth >= 768) return;
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            if (document.body.classList.contains('mobile-sidebar-open')) {
                closeMobileSidebar();
            } else {
                document.body.classList.add('mobile-sidebar-open');
                sidebar.classList.add('mobile-open');
            }
        });

        var sidebarCloseBtn = document.getElementById('sidebarToggle');
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', function(event){
                if (window.innerWidth >= 768) return;
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                closeMobileSidebar();
            });
        }
    }

    attachMobileSidebarHandler();
    document.addEventListener('DOMContentLoaded', attachMobileSidebarHandler);
})();