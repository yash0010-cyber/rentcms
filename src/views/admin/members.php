<section class="container py-5">
    <h2 class="fw-bold mb-4">Members</h2>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Occupation</th>
                        <th>Verified</th>
                        <th>Emergency Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?= htmlspecialchars($member['full_name'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($member['email'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($member['occupation'] ?? ''); ?></td>
                            <td><?= !empty($member['verified']) ? 'Yes' : 'No'; ?></td>
                            <td><?= htmlspecialchars($member['emergency_contact'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
