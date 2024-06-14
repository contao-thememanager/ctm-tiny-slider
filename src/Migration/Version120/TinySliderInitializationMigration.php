<?php

namespace ContaoThemeManager\TinySlider\Migration\Version120;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class TinySliderInitializationMigration extends AbstractMigration
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (
            !$schemaManager->tablesExist(['tl_style_manager', 'tl_content', 'tl_module'])
            || !array_key_exists('stylemanager', $schemaManager->listTableColumns('tl_content'))
            || !array_key_exists('stylemanager', $schemaManager->listTableColumns('tl_module'))
        ) {
            return false;
        }

        return $this->connection->fetchOne("
            SELECT (
                (Select COUNT(*) FROM tl_style_manager s 
                    LEFT JOIN tl_style_manager_archive a on s.pid = a.id 
                        WHERE a.identifier = 'sliderConfig' 
                            AND s.alias = 'init' 
                            AND s.passToTemplate = true
                )
                +
                (SELECT COUNT(*) 
                    FROM tl_content 
                        WHERE styleManager LIKE '%s:15:\"init-tns-slider\"%'
                )
                +
                (SELECT COUNT(*)
                    FROM tl_module
                        WHERE styleManager LIKE '%s:15:\"init-tns-slider\"%'
                )
            ) AS count;
        ") > 0;
    }

    /**
     * @throws Exception
     */
    private function runElementSliderInitializationMigration(string $table): void
    {
        $result = $this->connection->fetchAllAssociativeIndexed("SELECT 
            id, styleManager, cssID FROM " . $table . " WHERE styleManager LIKE '%s:15:\"init-tns-slider\"%'
        ");

        if (0 === count($result))
        {
            return;
        }

        foreach ($result as $id => $values)
        {
            if (
                !array_key_exists('styleManager', $values)
                || !array_key_exists('cssID', $values)
            ) {
                continue;
            }

            if (null !== $values['cssID'])
            {
                $cssID = StringUtil::deserialize($values['cssID'], true);
            }
            else
            {
                $cssID = ['',''];
            }

            if (2 !== count($cssID))
            {
                continue;
            }

            $cssID[1] = ltrim($cssID[1] . ' init-slider init-tns-slider');

            $styleManager = StringUtil::deserialize($values['styleManager'], true);

            if (
                (false !== strpos($cssID[1], 'init-slider init-tns-slider'))
                || !isset($styleManager['__vars__']['sliderConfig']['init'])
            ) {
                continue;
            }

            unset($styleManager['__vars__']['sliderConfig']['init']);
            $styleManager['sliderConfig_init'] = 'init-slider init-tns-slider';

            $this->connection->update($table,
                [
                    'cssID' => serialize($cssID),
                    'styleManager' => serialize($styleManager)
                ],
                [
                    'id' => (int) $id
                ]
            );
        }
    }

    /**
     * @throws Exception
     */
    private function runStyleManagerSliderInitializationTemplateVariableToClassMigration(): void
    {
        $sliderInit = $this->connection->fetchAllKeyValue("SELECT 
            s.id as id,
            s.cssClasses as cssClasses
                FROM tl_style_manager s 
                    LEFT JOIN tl_style_manager_archive a on s.pid = a.id 
                        WHERE a.identifier = 'sliderConfig' 
                            AND s.alias = 'init' 
                            AND s.passToTemplate = true"
        );

        if (0 === count($sliderInit))
        {
            return;
        }

        foreach ($sliderInit as $id => $value)
        {
            $cssClasses = StringUtil::deserialize($value, true);

            foreach ($cssClasses as $key => &$cssClass)
            {
                if (array_key_exists('key', $cssClass) && ('init-tns-slider' === $cssClass['key']))
                {
                    $cssClass['key'] = 'init-slider init-tns-slider';
                }
            }

            $this->connection->update('tl_style_manager',
                [
                    'cssClasses' => serialize($cssClasses),
                    'passToTemplate' => ''
                ],
                [
                    'id' => (int) $id
                ]
            );
        }
    }

    /**
     * @throws Exception
     */
    public function run(): MigrationResult
    {
        $this->runStyleManagerSliderInitializationTemplateVariableToClassMigration();
        $this->runElementSliderInitializationMigration('tl_module');
        $this->runElementSliderInitializationMigration('tl_content');

        return $this->createResult(true);
    }
}
