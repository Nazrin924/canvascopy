<?php session_start(); ?>

<header class='band' role='banner'>

		<!-- This file can be included in any file on this site.

		This is the code for the Search bar, which pops up when the magnifying glass is clicked.

		We have it so we can only search Cornell, because searching this site is meaningless in our context.
		Cornell Search (2014 only) -->
			<div class='band' id='cu-search-band'>
				<div class='container'>

					<div class='valign'>
						<div id='cu-search' class='options' role='search'>
							<form action='https://www.cornell.edu/search/'>
								<label for='search-form-query' class='sans'>Search:</label>
								<input type='text' id='search-form-query' name='q' value='' size='30'>
								<button name='btnG' id='search-form-submit' type='submit' value='go'><span class='fa fa-chevron-right'></span><span class='hidden'>Go</span></button>
								<div class='search-filters'>
									<h2 class='hidden'>Search Filters:</h2>
									<input type='radio' id='search-filters2' name='sitesearch' value='cornell' checked>
									<label for='search-filters2'>Cornell</label>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<!-- Cornell Identity -->
		<div class='band' id='cu-identity'>
			<div class='container'>
				<div id='banner-buttons'>
					<a class='mobile-button' id='mobile-nav' href='#aria-main-nav'>Main Navigation</a>
					<a class='mobile-button' id='search-button' href='#search-form'>Search Cornell</a>
				</div>

				<!-- cornell seal options (choose one)
					  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->

				<!-- a. cornell (45px, 75px, or 2014)
				<div id=''>
					<a href='https://registrar.cornell.edu'><img src='images/cornell_identity/registrar_2line_red.gif'  alt='Cornell University' width='240'></a>
				</div>-->
				<!-- b. unit (75px only) -->
				<!-- -->
				<style>#cu-logo a{ width: 400px; } #cu-logo.unit75 #unit-link {width: 400px;}</style>
				<div id="cu-logo" class="unit75">
					<a href="http://www.cornell.edu/"><img src="images/cornell_identity/culogo_hd.gif" width="179" height="45" alt="Cornell University"></a>
				</div>



				<!-- search options (choose one)
					  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->

				<!-- a. single line link -->
				<!--
				<div id='cu-search'>
					<a href='http://www.cornell.edu/search/'>Search Cornell</a>
				</div>
				 -->
				<!-- b. single line form -->
				<div id='cu-search'>
					<form role='search' method='get' id='searchform' action=''>
						<div id='search-form' class='no-label'>
							<label for='search-form-query'>Search:</label>
							<input type='text' id='search-form-query' name='q' value='Search' size='20' onblur='this.value = this.value || this.defaultValue;' onfocus='this.value == this.defaultValue && (this.value =''); this.select()'>
							<input type='submit' id='search-form-submit' name='submit' value='go'>
						</div>
					</form>
				</div>
				<!-- c. single line form with options (45px only) -->
				<!--
				<div id='cu-search' class='options'>
					<form role='search' method='get' id='searchform' action=''>
						<div id='search-form' class='no-label'>
							<div id='search-input'>
								<label for='search-form-query'>Search:</label>
								<input type='text' id='search-form-query' name='s' value='Search' size='20' onblur='this.value = this.value || this.defaultValue;' onfocus='this.value == this.defaultValue && (this.value =''); this.select()'>
								<input type='submit' id='search-form-submit' name='submit' value='go'>
							</div>
							<div id='search-filters'>
								<input type='radio' name='sitesearch' id='search-filters1' value='thissite' checked='checked'>
								<label for='search-filters1'>This Site</label>
								<input type='radio' name='sitesearch' id='search-filters2' value='cornell'>
								<label for='search-filters2'>Cornell</label>
							</div>
						</div>
					</form>
				</div>
				 -->
				<!-- d. two line form with options (75px only) -->
				<!--
				<div id='cu-search' class='options'>
					<form action='http://www.cornell.edu/search/' method='get' enctype='application/x-www-form-urlencoded'>
						<div id='search-form'>
							<label for='search-form-query'>SEARCH:</label>
							<input type='text' id='search-form-query' name='q' value='' size='20'>
							<input type='submit' id='search-form-submit' name='submit' value='go'>
							<div id='search-filters'>
								<input type='radio' id='search-filters1' name='tab' value='' checked='checked'>
								<label for='search-filters1'>Pages</label>
								<input type='radio' id='search-filters2' name='tab' value='people'>
								<label for='search-filters2'>People</label>
								<a href='http://www.cornell.edu/search/'>more options</a>
							</div>
						</div>
					</form>
				</div>
				 -->

			</div>
		</div>

		<div class="band -campaign -campaign-invert -photo-tint -photo-gradient -photo-gradient-topdown -photo-texture-light max-width favor-top" id="site-header">
			<div class="band stunt-double"></div>
			<div class="container">
				<div id="site-titles">
					<div class="valign">
						<a href="{{URL::route('index')}}">
							<h1>Canvas Resource Requests<h1>

							<!--<h2 class="sans">Custom Web Development</h2>-->
						</a>
					</div>
				</div>
			</div>
		</div>


	<div class="menu-revealer"></div>
		<nav class='band' id='main-navigation' aria-label='Main Navigation'>
			<div class='container'>
				<a id='mobile-home' href='#'><span class='hidden'>Search</span></a>
			</div>
		</nav>
</header>
