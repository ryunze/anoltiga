window.addEventListener('popstate', function (event) {
    const route = this.window.location.hash.split('#')[1].split('=')[1];
    $.get('/views/' + route + '.php', (res)=> {
        $('#app').html(res);
    });
});

$(document).ready(()=> {
    const routes = document.querySelectorAll('a[route]');
    routes.forEach((route)=> {
        route.addEventListener('click', (el)=> {
            document.querySelectorAll('a[route]').forEach((e)=> {
                e.classList.remove('active');
            })
            el.target.classList.add('active')
        })
    })
})