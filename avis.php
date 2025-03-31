<?php
$host = '127.0.0.1'; 
$dbname = 'PlanVoyages';
$username = 'root'; 
$password = 'rootroot'; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$sql = "SELECT nom, avis, note FROM avis";
$result = $conn->query($sql);
?>

<!-- From Uiverse.io by wztd --> 
<div class="avis-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card" style="--rating:<?php echo $row['note'] * 20; ?>">
            <div class="icon">📝</div>
            <div class="title">Avis de <?php echo htmlspecialchars($row['nom']); ?></div>
            <p class="description">"<?php echo htmlspecialchars($row['avis']); ?>"</p>
            <div class="rating"></div>
        </div>
    <?php endwhile; ?>
</div>

<?php $conn->close(); ?>

<style>
.avis-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
}

.card {
  --background: #fff;
  --cardShadow: rgba(32,33,37,.1);
  --cardShadowHover: rgba(32,33,37,.06);
  --cardShadowActive: rgba(32,33,37,.55);
  --cardBorder: #dbdce0;
  --cardBorderActive: #1a73e8;
  --textColor: #202125;
  --linkColor: #1967d2;
  --ratingColor: #faab00;
  width: 300px;
  height: auto;
  background: var(--background);
  color: var(--textColor);
  border: 1px solid var(--cardBorder);
  padding: 25px;
  box-shadow: 8px 8px 0 var(--cardShadow);
  transition: box-shadow .5s, transform .5s;
  border-radius: 10px;
  display: inline-block;
  text-align: center;
}

.card:hover {
  transform: translate(-2px, -4px);
  box-shadow: 16px 16px 0 var(--cardShadowHover);
}

.card > .icon,
.card > .title,
.card > .description {
  margin-bottom: 0.7em;
  cursor: default;
  user-select: none;
}

.card > .title {
  margin-top: 1.5em;
  font-weight: bold;
  font-size: 1.3em;
}

.card > .description {
  line-height: 1.5em;
  min-height: 6em;
}

.card > .icon {
  font-size: 3.5em;
  margin-bottom: .2em;
}

.card > .rating {
  font-size: 1.5em;
  margin-bottom: 0.8em;
  color: var(--ratingColor);
  font-weight: bold;
  position: relative;
  width: max-content;
  margin-left: auto;
  margin-right: auto;
}

.card > .rating:before {
  content: "☆☆☆☆☆";
}

.card > .rating:after {
  content: "★★★★★";
  position: absolute;
  left: 0;
  z-index: 0;
  width: calc(var(--rating) * 1%);
  overflow: hidden;
}
</style>