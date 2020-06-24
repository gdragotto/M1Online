function ThreeDeeTouch_init() {
    ThreeDeeTouch.isAvailable(function (avail) {
        ThreeDeeTouch.enableLinkPreview();
        ThreeDeeTouch.configureQuickActions([
            {
                type: 'random',
                title: 'Random',
                subtitle: 'Get a random article.',
                iconTemplate: 'Random'
              },
            {
                type: 'search',
                title: 'Search',
                iconTemplate: 'Search'
              },

            {
                type: 'account',
                title: 'Account',
                iconTemplate: 'Account'
              },
            ]);
    });
}

document.addEventListener('deviceready', function () {
    ThreeDeeTouch.onHomeIconPressed = function (payload) {
        console.log("Icon pressed. Type: " + payload.type + ". Title: " + payload.title + ".");
        if (payload.type == 'fav') {

        } else if (payload.type == 'search') {
            menu.setMainPage('search2.html', {
                closeMenu: true
            });
        } else if (payload.type == 'account') {
            menu.setMainPage('account.html', {
                closeMenu: true
            });
        } else if (payload.type == 'random') {
            menu.setMainPage('random.html', {
                closeMenu: true
            });
        } else {
            console.log(JSON.stringify(payload));
        }
    }
}, false);