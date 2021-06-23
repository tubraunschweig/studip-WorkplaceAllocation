<?php
/**
 * 002_deactivate_in_course migration
 *
 * User: jayjay
 * Date: 28.03.18
 * Time: 17:23
 */

class DeactivateInCourses extends Migration
{
    public function up()
    {
        /** @var $semClasses SemClass[] */
        $semClasses = SemClass::getClasses();

        foreach ($semClasses as $semClass) {
            $modules = $semClass->getModules();

            $modules['WorkplaceAllocation'] = [
                'activated' => 0,
                'sticky' => 1
            ];

            $semClass->setModules($modules);
            $semClass->store();
        }
    }

    public function down()
    {
    }
}
