<?php

declare(strict_types=1);

namespace Pest\Stressless\ResultPrinters;

use Pest\Stressless\Blocks\NetworkDuration;
use Pest\Stressless\Blocks\ResponseDuration;
use Pest\Stressless\Blocks\ServerDuration;
use Pest\Stressless\Blocks\SuccessRate;
use Pest\Stressless\ValueObjects\Result;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

use function Termwind\render;
use function Termwind\terminal;

/**
 * @internal
 */
final readonly class Blocks
{
    private const BLOCK_SIZE = 15;

    private const ALL_BLOCKS_IN_ROW = 4;

    private const TWO_BLOCKS_IN_ROW = 2;

    private const MIN_SPACEWIDTH = 5;

    private const MAX_SPACEWIDTH = 15;

    private const SUCCESS_RATE = <<<'EOD'
    <%success_rate_color%>%block_size%</>
    <fg=white;options=bold;%success_rate_color%>   %success_rate%   </>
    <%success_rate_color%>%block_size%</>
    EOD;

    private const RESPONSE_DURATION = <<<'EOD'
    <%response_time_color%>%block_size%</>
    <fg=white;options=bold;%response_time_color%>   %response_time%   </>
    <%response_time_color%>%block_size%</>
    EOD;

    private const NETWORK_DURATION = <<<'EOD'
    <%ttfb_color%>%block_size%</>
    <fg=white;options=bold;%ttfb_color%>   %ttfb%   </>
    <%ttfb_color%>%block_size%</>
    EOD;

    private const SERVER_DURATION = <<<'EOD'
    <%server_duration_color%>%block_size%</>
    <fg=white;options=bold;%server_duration_color%>   %server_duration%   </>
    <%server_duration_color%>%block_size%</>
    EOD;

    public function print(Result $result): void
    {
        render(<<<'HTML'
            <div class="mx-2 my-1 text-gray">
                <span class="text-red">■</span>
                <span class="ml-1">0-49</span>
                <span class="text-yellow ml-2">■</span>
                <span class="ml-1">50-89</span>
                <span class="text-green ml-2">■</span>
                <span class="ml-1">90-100</span>
            </div>
        HTML);

        $successRate = new SuccessRate($result);
        $responseDuration = new ResponseDuration($result);
        $networkDuration = new NetworkDuration($result);
        $serverDuration = new ServerDuration($result);

        $templates = [
            '%success_rate%' => $successRate->value(),
            '%success_rate_color%' => "bg={$successRate->color()}",
            '%response_time%' => $responseDuration->value(),
            '%response_time_color%' => "bg={$responseDuration->color()}",
            '%ttfb%' => $networkDuration->value(),
            '%ttfb_color%' => "bg={$networkDuration->color()}",
            '%server_duration%' => $serverDuration->value(),
            '%server_duration_color%' => "bg={$serverDuration->color()}",
            '%subtitle%' => 'fg=white;options=bold;fg=white',
        ];
        $disposition = self::ALL_BLOCKS_IN_ROW;
        $spaceWidth = $this->getSpaceWidth(terminal()->width(), self::BLOCK_SIZE, $disposition);

        if (terminal()->width() < ((self::BLOCK_SIZE * $disposition) + 5 * $spaceWidth)) {
            $disposition = self::TWO_BLOCKS_IN_ROW;
            $spaceWidth = $this->getSpaceWidth(terminal()->width(), self::BLOCK_SIZE, $disposition);
        }

        $templates = [...$templates, '%block_size%' => str_pad('', self::BLOCK_SIZE)];

        $styleDefinition = clone Table::getStyleDefinition('compact');

        $styleDefinition->setVerticalBorderChars(
            str_pad('', (int) floor($spaceWidth / 2)), // outside
            '' // inside
        );

        $styleDefinition->setPadType(STR_PAD_BOTH);
        $styleDefinition->setCellRowContentFormat('%s');

        $table = new Table(new ConsoleOutput());
        $table->setStyle($styleDefinition);

        $table->setColumnWidth(0, self::BLOCK_SIZE + $spaceWidth);
        $table->setColumnWidth(1, self::BLOCK_SIZE + $spaceWidth);
        $table->setColumnWidth(2, self::BLOCK_SIZE + $spaceWidth);
        $table->setColumnWidth(3, self::BLOCK_SIZE + $spaceWidth);

        if ($disposition === self::ALL_BLOCKS_IN_ROW) {
            $table->setRows([
                [
                    strtr(self::SUCCESS_RATE, $templates),
                    strtr(self::RESPONSE_DURATION, $templates),
                    strtr(self::NETWORK_DURATION, $templates),
                    strtr(self::SERVER_DURATION, $templates),
                ],
                ['', '', '', ''],
                [
                    strtr('<%subtitle%> Success Rate </>', $templates),
                    strtr('<%subtitle%> Response </>', $templates),
                    strtr('<%subtitle%> Network </>', $templates),
                    strtr('<%subtitle%> Server </>', $templates),
                ],
            ]);
        }

        if ($disposition === self::TWO_BLOCKS_IN_ROW) {
            $table->setRows([
                [
                    strtr(self::SUCCESS_RATE, $templates),
                    strtr(self::RESPONSE_DURATION, $templates),
                ],
                ['', ''],
                [
                    strtr('<%subtitle%> Success Rate </>', $templates),
                    strtr('<%subtitle%> Response </>', $templates),
                ],
                ['', ''],
                [
                    strtr(self::NETWORK_DURATION, $templates),
                    strtr(self::SERVER_DURATION, $templates),
                ],
                ['', ''],
                [
                    strtr('<%subtitle%> Network </>', $templates),
                    strtr('<%subtitle%> Server </>', $templates),
                ],
            ]);
        }

        $table->render();
    }

    /**
     * Total width of terminal - block size * disposition (4 or 2) / number of space block.
     */
    private function getSpaceWidth(int $totalWidth, int $blockSize, int $disposition): int
    {
        $spaceWidth = (int) floor(($totalWidth - $blockSize * $disposition) / ($disposition + 1));

        if ($spaceWidth > self::MAX_SPACEWIDTH) {
            $spaceWidth = self::MAX_SPACEWIDTH;
        }

        if ($spaceWidth < self::MIN_SPACEWIDTH) {
            return self::MIN_SPACEWIDTH;
        }

        return $spaceWidth;
    }
}
