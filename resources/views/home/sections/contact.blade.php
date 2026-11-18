<section class="contact py-5 bg-light-section" id="contact">
    <div class="container py-4">

        {{-- Section Header --}}
        <div class="text-center mb-5">
            <span class="badge-tag mb-2 d-inline-block reveal">Reach Our Team</span>
            <h2 class="section-title reveal" style="transition-delay:.06s">Baseline IT Development Contact Desk</h2>
            <p class="section-subtitle reveal" style="transition-delay:.12s">
                Have questions regarding internal project access, client collaboration, or technical workspace configuration? Reach out to our internal IT support.
            </p>
        </div>

        <div class="row g-4 justify-content-center">

            {{-- Contact Information Card --}}
            <div class="col-lg-4 reveal-left">
                <div class="contact-info-card h-100 p-4 p-md-5 rounded-4 text-white shadow">
                    <h4 class="fw-bold text-white mb-3">Baseline IT Development</h4>
                    <p class="text-white-50 fs-14 mb-4">
                        Baseline IT Development Pvt Ltd official internal helpdesk and technical administration.
                    </p>

                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <span class="contact-icon-badge">
                                <i class="bi bi-building-fill" style="color:#60A5FA"></i>
                            </span>
                            <div>
                                <small class="text-white-50 d-block fs-11">Company</small>
                                <span class="fs-14 fw-medium text-white">Baseline IT Development Pvt Ltd</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <span class="contact-icon-badge">
                                <i class="bi bi-envelope-at-fill" style="color:#60A5FA"></i>
                            </span>
                            <div>
                                <small class="text-white-50 d-block fs-11">Official Email</small>
                                <span class="fs-14 fw-medium text-white">support@baselineitdevelopment.com</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <span class="contact-icon-badge">
                                <i class="bi bi-clock-history" style="color:#60A5FA"></i>
                            </span>
                            <div>
                                <small class="text-white-50 d-block fs-11">Working Hours</small>
                                <span class="fs-14 fw-medium text-white">Mon &ndash; Fri: 9:30 AM &ndash; 6:30 PM</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-white bg-opacity-10 mt-auto">
                        <div class="d-flex align-items-center gap-2 fs-13 mb-1" style="color:#D97706">
                            <i class="bi bi-shield-check"></i>
                            <span class="fw-semibold">Secure Internal Portal</span>
                        </div>
                        <small class="text-white-50 fs-12">
                            Access restricted to authorized employees, team leads, and project managers.
                        </small>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="col-lg-7 reveal-right">
                <div class="contact-form-card p-4 p-md-5 rounded-4 bg-white border shadow-sm">
                    <h4 class="fw-bold text-navy mb-3">Send an Inquiry or Support Request</h4>
                    <form onsubmit="event.preventDefault(); alert('Your message has been sent to Baseline IT Development team.');">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-navy">Employee / Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" placeholder="e.g. Rahul Sharma" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-navy">Work Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control border-start-0 ps-0" placeholder="e.g. rahul@baselineitdevelopment.com" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-navy">Organizational Role</label>
                                <select class="form-select">
                                    <option selected>Select Role</option>
                                    <option value="admin">Administrator / Management</option>
                                    <option value="pm">Project Manager</option>
                                    <option value="tl">Team Leader</option>
                                    <option value="employee">Developer / Software Engineer</option>
                                    <option value="client">Client Representative</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-navy">Subject / Project Reference</label>
                                <input type="text" class="form-control" placeholder="e.g. Sprint Access / Task Allocation" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-13 fw-semibold text-navy">Message Details</label>
                                <textarea class="form-control" rows="4" placeholder="Describe your query or requirement..." required></textarea>
                            </div>

                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-brand btn-lg-custom w-100">
                                    <i class="bi bi-send-fill"></i> Submit to Baseline IT Desk
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</section>
