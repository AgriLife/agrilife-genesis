
# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "css"
sass_dir = "css/src"
images_dir = "img"
javascripts_dir = "js"
fonts_dir = "font"

# Can be :expanded, :nested, :compact or :compressed
output_style = :compressed

# Allows relative paths to be used
relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
# line_comments = false
# color_output = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass sass scss && rm -rf sass && mv scss sass
preferred_syntax = :scss

sass_options = {:debug_info => false}
