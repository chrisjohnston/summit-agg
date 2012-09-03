<table>
	<thead>
		<th>Booking Date</th>
	</thead>
<?php foreach($bookings AS $booking) : ?>
	<tr>
		<?php var_dump($booking) ?>
	</tr>
<?php endforeach; ?>
</table>