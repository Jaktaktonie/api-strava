<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($documentationTitle); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo e(l5_swagger_asset($documentation, 'swagger-ui.css')); ?>">
    <link rel="icon" type="image/png" href="<?php echo e(l5_swagger_asset($documentation, 'favicon-32x32.png')); ?>" sizes="32x32"/>
    <link rel="icon" type="image/png" href="<?php echo e(l5_swagger_asset($documentation, 'favicon-16x16.png')); ?>" sizes="16x16"/>
    <style>
    html
    {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
        box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
    }
    </style>
    <?php if(config('l5-swagger.defaults.ui.display.dark_mode')): ?>
        <style>
            body#dark-mode,
            #dark-mode .scheme-container {
                background: #1b1b1b;
            }
            #dark-mode .scheme-container,
            #dark-mode .opblock .opblock-section-header{
                box-shadow: 0 1px 2px 0 rgba(255, 255, 255, 0.15);
            }
            #dark-mode .operation-filter-input,
            #dark-mode .dialog-ux .modal-ux,
            #dark-mode input[type=email],
            #dark-mode input[type=file],
            #dark-mode input[type=password],
            #dark-mode input[type=search],
            #dark-mode input[type=text],
            #dark-mode textarea{
                background: #343434;
                color: #e7e7e7;
            }
            #dark-mode .title,
            #dark-mode li,
            #dark-mode p,
            #dark-mode table,
            #dark-mode label,
            #dark-mode .opblock-tag,
            #dark-mode .opblock .opblock-summary-operation-id,
            #dark-mode .opblock .opblock-summary-path,
            #dark-mode .opblock .opblock-summary-path__deprecated,
            #dark-mode h1,
            #dark-mode h2,
            #dark-mode h3,
            #dark-mode h4,
            #dark-mode h5,
            #dark-mode .btn,
            #dark-mode .tab li,
            #dark-mode .parameter__name,
            #dark-mode .parameter__type,
            #dark-mode .prop-format,
            #dark-mode .loading-container .loading:after{
                color: #e7e7e7;
            }
            #dark-mode .opblock-description-wrapper p,
            #dark-mode .opblock-external-docs-wrapper p,
            #dark-mode .opblock-title_normal p,
            #dark-mode .response-col_status,
            #dark-mode table thead tr td,
            #dark-mode table thead tr th,
            #dark-mode .response-col_links,
            #dark-mode .swagger-ui{
                color: wheat;
            }
            #dark-mode .parameter__extension,
            #dark-mode .parameter__in,
            #dark-mode .model-title{
                color: #949494;
            }
            #dark-mode table thead tr td,
            #dark-mode table thead tr th{
                border-color: rgba(120,120,120,.2);
            }
            #dark-mode .opblock .opblock-section-header{
                background: transparent;
            }
            #dark-mode .opblock.opblock-post{
                background: rgba(73,204,144,.25);
            }
            #dark-mode .opblock.opblock-get{
                background: rgba(97,175,254,.25);
            }
            #dark-mode .opblock.opblock-put{
                background: rgba(252,161,48,.25);
            }
            #dark-mode .opblock.opblock-delete{
                background: rgba(249,62,62,.25);
            }
            #dark-mode .loading-container .loading:before{
                border-color: rgba(255,255,255,10%);
                border-top-color: rgba(255,255,255,.6);
            }
            #dark-mode svg:not(:root){
                fill: #e7e7e7;
            }
            #dark-mode .opblock-summary-description {
                color: #fafafa;
            }
        </style>
    <?php endif; ?>
</head>

<body <?php if(config('l5-swagger.defaults.ui.display.dark_mode')): ?> id="dark-mode" <?php endif; ?>>
<div id="swagger-ui"></div>

<script src="<?php echo e(l5_swagger_asset($documentation, 'swagger-ui-bundle.js')); ?>"></script>
<script src="<?php echo e(l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js')); ?>"></script>
<script>
    window.onload = function() {
        const urls = [];

        <?php $__currentLoopData = $urlsToDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            urls.push({name: "<?php echo e($title); ?>", url: "<?php echo e($url); ?>"});
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            urls: urls,
            "urls.primaryName": "<?php echo e($documentationTitle); ?>",
            operationsSorter: <?php echo isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null'; ?>,
            configUrl: <?php echo isset($configUrl) ? '"' . $configUrl . '"' : 'null'; ?>,
            validatorUrl: <?php echo isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null'; ?>,
            oauth2RedirectUrl: "<?php echo e(route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath)); ?>",

            requestInterceptor: function(request) {
                request.headers['X-CSRF-TOKEN'] = '<?php echo e(csrf_token()); ?>';
                return request;
            },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout",
            docExpansion : "<?php echo config('l5-swagger.defaults.ui.display.doc_expansion', 'none'); ?>",
            deepLinking: true,
            filter: <?php echo config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false'; ?>,
            persistAuthorization: "<?php echo config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false'; ?>",

        })

        window.ui = ui

        <?php if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type'))): ?>
        ui.initOAuth({
            usePkceWithAuthorizationCodeGrant: "<?php echo (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant'); ?>"
        })
        <?php endif; ?>
    }
</script>
</body>
</html>
<?php /**PATH /Users/sami/apistrava/api-strava/vendor/darkaonline/l5-swagger/src/../resources/views/index.blade.php ENDPATH**/ ?>