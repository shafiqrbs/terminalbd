if [ -d dist ]; then
    rm dist -r;
fi
mkdir dist;
node node_modules/minifier/index.js --output dist/jquery.sollist.min.js jquery.sollist.js;
cp jquery.sollist.js dist/;

cp sollist.css tmp.css;
node themes-css-generator.js >> tmp.css
cp tmp.css dist/jquery.sollist.css;
node node_modules/minifier/index.js --output dist/jquery.sollist.min.css tmp.css;
rm tmp.css;

cp -r sollist-themes dist/;