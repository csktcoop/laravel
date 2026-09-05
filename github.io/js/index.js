/* AGENT: Required page controller. It populates content, assets, metadata, and navigation state. */
(function () {
	'use strict';

	function readSiteData() {
		const element = document.getElementById('site-data');

		return element ? JSON.parse(element.textContent) : {};
	}

	function updateDocumentMetadata(site) {
		if (site.name && site.role) {
			document.title = `${site.name} - ${site.role}`;
			document.querySelector('meta[name="author"]')?.setAttribute('content', site.name);
			document.querySelector('meta[property="og:title"]')?.setAttribute('content', `${site.name} - ${site.role}`);
		}

		if (site.siteUrl) {
			document.querySelector('meta[property="og:url"]')?.setAttribute('content', site.siteUrl);
			document.querySelector('link[rel="canonical"]')?.setAttribute('href', site.siteUrl);
		}
	}

	function populateSiteValues(site) {
		document.querySelectorAll('[data-site]').forEach((element) => {
			const value = site[element.dataset.site];

			if (value) element.textContent = value;
		});
	}

	function populateAssetLinks(site) {
		document.querySelectorAll('[data-cv-link]').forEach((link) => {
			link.href = site.cv || '#';
		});

		document.querySelectorAll('[data-email-link]').forEach((link) => {
			if (site.email) link.href = `mailto:${site.email}`;
		});

		document.querySelectorAll('[data-portrait]').forEach((image) => {
			image.alt = site.name ? `Portrait of ${site.name}` : 'Portrait';
		});
	}

	function renderHero(site) {
		document.getElementById('hero-content').innerHTML = `
			<p class="eyebrow">${site.role}</p>
			<h1 id="hero-title">${site.name}</h1>
			<p class="hero-statement">${site.objective}</p>
			<div class="hero-actions"><a class="button button-primary" href="#projects">View selected work</a><a class="button button-secondary" href="#contact">Get in touch</a></div>`;
		document.getElementById('portrait-caption').innerHTML = `<span>${site.location}</span><span aria-hidden="true">/</span><span>${site.role}</span>`;
	}

	function renderAbout(site) {
		document.getElementById('about-content').innerHTML = `
			<p class="lead">${site.objective}</p>
			<div class="about-copy"><p>${site.about}</p><div class="facts"><span><span aria-hidden="true">📍</span> ${site.location}</span><span><span aria-hidden="true">🧠</span> AI focused</span><span><span aria-hidden="true">🤝</span> Open to collaboration</span></div></div>`;
	}

	function renderSkills(site) {
		document.getElementById('skill-list').innerHTML = site.skills.map((skill) => `<li>${skill}</li>`).join('');
	}

	function renderExperience(site) {
		document.getElementById('experience-content').innerHTML = site.experience.map((entry, index) => `
			<article class="timeline-item"><span class="timeline-marker">${String(index + 1).padStart(2, '0')}</span><div><p class="meta">${entry.period} / ${entry.company}</p><h3>${entry.role}</h3><p>${entry.summary}</p><p>${entry.details}</p></div></article>`).join('');
	}

	function renderProjects(site) {
		document.getElementById('projects-content').innerHTML = site.projects.map((project) => `
			<article class="project-card"><p class="meta">${project.type}</p><h3>${project.name}</h3><p>${project.summary}</p><a class="project-link" href="${project.url}" target="_blank" rel="noopener noreferrer">Visit public site <span aria-hidden="true">↗</span></a></article>`).join('');
	}

	function renderEducation(site) {
		document.getElementById('education-content').innerHTML = site.education.map((item) => `
			<article class="education-item"><p class="meta">${item.period}</p><h3>${item.title}</h3><p>${item.institution}</p><span>${item.details}</span></article>`).join('');
	}

	function renderAchievements(site) {
		document.getElementById('achievements-content').innerHTML = `<p class="lead">Professional impact is often confidential by design.</p><p>${site.achievements}</p>`;
	}

	function renderCertifications(site) {
		document.getElementById('certifications-content').innerHTML = `<p class="lead">Recent learning and security credentials.</p><ul class="certification-links">${site.certifications.map((certificate) => `<li><a href="${certificate.url}" target="_blank" rel="noopener noreferrer">${certificate.name}<span aria-hidden="true">↗</span></a></li>`).join('')}</ul>`;
	}

	function focusMainContent() {
		document.getElementById('skip-link')?.addEventListener('click', () => {
			const main = document.getElementById('main-content');

			main?.focus({ preventScroll: true });
			main?.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
		});
	}

	function activateSectionNavigation() {
		const sections = [...document.querySelectorAll('main section[id]')];
		const links = [...document.querySelectorAll('.nav-links a')];
		const linksById = new Map(links.map((link) => [link.hash.slice(1), link]));

		if (!('IntersectionObserver' in window)) return;

		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;

				links.forEach((link) => link.removeAttribute('aria-current'));
				linksById.get(entry.target.id)?.setAttribute('aria-current', 'page');
			});
		}, { rootMargin: '-35% 0px -55% 0px', threshold: 0 });

		sections.forEach((section) => observer.observe(section));
	}

	function initializePortfolio() {
		const site = readSiteData();

		updateDocumentMetadata(site);
		populateSiteValues(site);
		populateAssetLinks(site);
		renderHero(site);
		renderAbout(site);
		renderSkills(site);
		renderExperience(site);
		renderProjects(site);
		renderEducation(site);
		renderAchievements(site);
		renderCertifications(site);
		populateAssetLinks(site);
		focusMainContent();
		activateSectionNavigation();
	}

	initializePortfolio();
})();
