<style>
    /* The whole thing */
    .custom-menu {
        display: none;
        z-index: 1000;
        position: absolute;
        overflow: hidden;
        border: 1px solid #CCC;
        white-space: nowrap;
        font-family: sans-serif;
        background: #FFF;
        color: #333;
        border-radius: 5px;
        padding: 0;
    }

    /* Each of the items in the list */
    .custom-menu li {
        padding: 8px 12px;
        cursor: pointer;
        list-style-type: none;
        transition: all .3s ease;
        user-select: none;
    }

    .custom-menu li a {
        text-decoration: none;
        color: inherit;
    }

    .custom-menu li:hover {
        background-color: #DEF;
    }
</style>

<ul class='custom-menu'>
    <li data-action="first"><a href="index.php" target="_blank">Buka di tab baru</a></li>
</ul>

<section id="sidebar">
    <ul>
        <li>
            <a routelink href="/?route=sms-sender" class="<?= (active_page('sms-sender') ? 'active' : '')?>">SMS Sender</a>
        </li>
        <li>
            <a routelink href="/?route=sms-bulk" class="<?= (active_page('sms-bulk') ? 'active' : '')?>">SMS Bulk</a>
        </li>
        <li>
            <a routelink href="/?route=wa-webs" class="<?= (active_page('wa-webs') ? 'active' : '')?>">WA Webs</a>
        </li>
    </ul>
</section>

<script>
    // Trigger action when the contexmenu is about to be shown
    $(document).bind("contextmenu", function (event) {

        // console.log(event)

        if (event.target.attributes[0].name == "routelink") {

            // Avoid the real one
            event.preventDefault();

            $('.custom-menu li a').attr('href', event.target.attributes[1].value);
            // Show contextmenu
            $(".custom-menu").finish().toggle(100).

            // In the right position (the mouse)
            css({
                top: event.pageY + "px",
                left: event.pageX + "px"
            });
        }


    });


    // If the document is clicked somewhere
    $(document).bind("mousedown", function (e) {

        // If the clicked element is not the menu
        if (!$(e.target).parents(".custom-menu").length > 0) {

            // Hide it
            $(".custom-menu").hide(100);
        }
    });


    // If the menu element is clicked
    $(".custom-menu li").click(function () {
        // Hide it AFTER the action was triggered
        $(".custom-menu").hide(100);
    });
</script>

<script>
    $(document).ready(function () {

        const viewName = document.location.search.split('?view=')[1];
        const activeNav = $("#sidebar ul li a[data-view=" + viewName + "]")[0];
        if (activeNav != undefined) {
            activeNav.classList.add('active');
        }
    });
</script>