<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bar','data' => ['navlinks' => [
    ['label' => 'Calendrier', 'route' => 'formateur_calendar', 'class' => 'active', 'icon' => 'fas fa-calendar-alt'],
    ['label' => 'Dashboard', 'route' => 'dashboard', 'class' => '', 'icon' => 'fas fa-chart-bar'],
]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['navlinks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['label' => 'Calendrier', 'route' => 'formateur_calendar', 'class' => 'active', 'icon' => 'fas fa-calendar-alt'],
    ['label' => 'Dashboard', 'route' => 'dashboard', 'class' => '', 'icon' => 'fas fa-chart-bar'],
])]); ?>
    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/calendar.js'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>

    <script src='assets/js/fullcalendar.js' type="text/javascript"></script>
    <script>
        const data = JSON.parse('<?php echo json_encode($modules, JSON_HEX_APOS); ?>');
        const holidays = JSON.parse('<?php echo json_encode($holidays); ?>');
    </script>

    <div class="container mt-12 pt-10">
        <div class="call">
            <div class="cal-scroll col-md-12">
                <div id='calendar'></div>
                <div style='clear:both'></div>

            </div>
            <div id="call"></div>
            <hr>
            <div class="col-md-12">
                <div id="weeklyUpdateContainer"></div>
            </div>
            <br>
            <form id="saveChangesForm" style="display: none;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="moduleData" id="moduleDataInput">
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>



<?php /**PATH C:\school\stage\calendar_ista\calendar_ista\ista_management\resources\views/formateur/calendar.blade.php ENDPATH**/ ?>