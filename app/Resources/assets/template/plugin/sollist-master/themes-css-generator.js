var  services = ['facebook',
	'instagram',
	'twitter',
	'pinterest',
	'tumblr',
	'vine' ,
	'googleplus',
	'behance',
	'dribbble',
	'github',
	'skype',
	'twitch',
	'youtube',
	'linkedin',
	'soundcloud',
	'livejournal',
	'bitbucket',
	'vk',
	'deviantart',
	'digg',
	'flickr',
	'foursquare',
	'steam',
	'envato',
	'stackoverflow',
	'reddit',
	'kickstarter',
	'email',
	'medium',
	'quora',
	'vimeo'];
var themes = ['lee-gargano-circle-color', 
			'lee-gargano-circle-white',
			'lee-gargano-square-white',
			'lee-gargano-square-color',
			'fuel',
			'handdrawn',
			'martz90-hex',
			'victor-bejar',
			'mikymeg-color',
			'mikymeg-grey',
			'light-circle',
			];
var extension = 'png';
var base_path = 'sollist-themes/';
var service;
var theme;
for (var themeKey in themes){
	for (var serviceKey in services){
        service = services[serviceKey];
        theme = themes[themeKey];
		process.stdout.write(".sl-container .sl-item a." + theme + "." + service +" {background-image:url('" + base_path+theme+"/"+service +"."+extension + "')" +"}\n");
    }
}