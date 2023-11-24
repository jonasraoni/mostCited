{**
 * templates/mostCited.tpl
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief Displays the most cited submissions widget.
 *}
{if $mostCitedSubmissions && count($mostCitedSubmissions) > 0}
	{if $mostCitedPosition}
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				let mvDiv = document.querySelector('.most-cited');
				mvDiv.parentElement.append(mvDiv);
				mvDiv.style.display = 'block';
			});
		</script>
		<style>
			.most-cited {
				display: none;
			}
		</style>
	{/if}
	<div class="most-cited obj_article_summary">
		<h2 class="most-cited-headline">{if $mostCitedHeadline[$currentLocale]}{$mostCitedHeadline[$currentLocale]}{else}{translate key="plugins.generic.mostCited.header.default"}{/if}</h2>
		{foreach from=$mostCitedSubmissions item="submission"}
			<div class="most-cited-content">
				<div class="most-cited-title">
					<a href={url page="article" op="view" path=$submission['submissionId']}>
						{$submission['submissionTitle']|strip_unsafe_html}
					</a>
				</div>
				<div class="most-cited-subtitle">
					{if $submission['submissionSubtitle']}
						{$submission['submissionSubtitle']|strip_unsafe_html}
					{/if}
				</div>
				<div class="most-cited-author">
					<div class="font-italic">{$submission['submissionAuthor']|strip_unsafe_html}</div>
					<div>
						<span class="badge">{$submission['citations']|strip_unsafe_html}&nbsp;<i class="fa fa-quote-right" aria-hidden="true"></i></span>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
{/if}
